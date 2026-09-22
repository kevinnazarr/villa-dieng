<?php

namespace Tests\Feature;

use App\Actions\CancelReservationAction;
use App\Actions\ConfirmReservationAction;
use App\Actions\CreateReservationAction;
use App\Actions\ExpirePendingReservationAction;
use App\Actions\InitiatePaymentAction;
use App\Actions\ProcessPaymentAction;
use App\Domain\PaymentStateMachine;
use App\Domain\ReservationStateMachine;
use App\Enums\PaymentStatus;
use App\Enums\ReservationStatus;
use App\Exceptions\BookingConflictException;
use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use App\Services\SandboxPaymentGateway;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\PgTestCase;

/**
 * Booking domain suite on real PG: guest checkout, EXCLUDE conflict →
 * 409 exception, payment success/fail/timeout, cancel/confirm, expiry,
 * state-machine guards, idempotency reuse.
 */
class BookingDomainTest extends PgTestCase
{
    private function cabin(): Cabin
    {
        return Cabin::factory()->create();
    }

    /** @return array<string, mixed> */
    private function payload(Cabin $cabin, array $over = []): array
    {
        return array_merge([
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-04',
            'adults' => 2,
            'children' => 0,
            'guest_name' => 'Guest One',
            'guest_email' => 'Guest@Example.com',
            'guest_phone' => '08123456789',
        ], $over);
    }

    public function test_guest_checkout_creates_user_payment_event(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));

        $this->assertSame(ReservationStatus::PendingPayment, $r->status);
        $this->assertSame('guest@example.com', $r->guest_email);
        $this->assertSame('guest@example.com', $r->user->email);
        $this->assertNull($r->user->password);
        $this->assertSame('2250000.00', (string) $r->total);
        $this->assertNotNull($r->expires_at);
        $this->assertCount(1, $r->payments);
        $this->assertSame(PaymentStatus::Pending, $r->payments->first()->status);
        $this->assertSame('created', $r->events->first()->event_type->value);
    }

    public function test_guest_reuse_without_overwrite(): void
    {
        $cabin = $this->cabin();
        $user = User::factory()->create(['email' => 'guest@example.com', 'name' => 'Original']);

        $r = app(CreateReservationAction::class)->execute($this->payload($cabin, [
            'guest_name' => 'Someone Else',
            'check_in' => '2026-12-01',
            'check_out' => '2026-12-03',
        ]));

        $this->assertSame($user->id, $r->user_id);
        $this->assertSame('Original', $user->refresh()->name);
    }

    public function test_overlapping_second_booking_throws_conflict(): void
    {
        $cabin = $this->cabin();
        $action = app(CreateReservationAction::class);
        $action->execute($this->payload($cabin));

        $this->expectException(BookingConflictException::class);
        $action->execute($this->payload($cabin, [
            'guest_email' => 'other@example.com',
            'check_in' => '2026-11-02',
            'check_out' => '2026-11-05',
        ]));
    }

    public function test_invalid_dates_and_capacity_rejected(): void
    {
        $action = app(CreateReservationAction::class);

        $this->expectException(InvalidArgumentException::class);
        $action->execute($this->payload($this->cabin(), [
            'check_in' => '2026-11-05',
            'check_out' => '2026-11-01',
        ]));
    }

    public function test_capacity_exceeded_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        app(CreateReservationAction::class)->execute($this->payload($this->cabin(), ['adults' => 99]));
    }

    public function test_payment_success_confirms_reservation(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        $payment = app(InitiatePaymentAction::class)->execute($r);

        $payment = app(ProcessPaymentAction::class, ['gateway' => new SandboxPaymentGateway])
            ->execute($payment, SandboxPaymentGateway::SCENARIO_SUCCESS);

        $this->assertSame(PaymentStatus::Paid, $payment->status);
        $this->assertNotNull($payment->paid_at);
        $this->assertSame(ReservationStatus::Confirmed, $r->refresh()->status);
        $this->assertTrue($r->refresh()->events->pluck('event_type')->contains(
            fn ($t) => $t->value === 'payment_succeeded'
        ));
    }

    public function test_payment_failure_leaves_pending(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        $payment = app(InitiatePaymentAction::class)->execute($r);

        $payment = app(ProcessPaymentAction::class, ['gateway' => new SandboxPaymentGateway])
            ->execute($payment, SandboxPaymentGateway::SCENARIO_FAIL);

        $this->assertSame(PaymentStatus::Failed, $payment->status);
        $this->assertSame(ReservationStatus::PendingPayment, $r->refresh()->status);

        // Retry creates a new attempt with a fresh reference.
        $retry = app(InitiatePaymentAction::class)->execute($r->refresh());
        $this->assertNotSame($payment->id, $retry->id);
    }

    public function test_payment_timeout_parks_unknown(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        $payment = app(InitiatePaymentAction::class)->execute($r);

        $payment = app(ProcessPaymentAction::class, ['gateway' => new SandboxPaymentGateway])
            ->execute($payment, SandboxPaymentGateway::SCENARIO_TIMEOUT);

        $this->assertSame(PaymentStatus::Unknown, $payment->status);
        $this->assertSame(ReservationStatus::PendingPayment, $r->refresh()->status);
    }

    public function test_idempotency_key_reuses_open_payment(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        $init = app(InitiatePaymentAction::class);

        $first = $init->execute($r, ['idempotency_key' => 'key-1']);
        $second = $init->execute($r->refresh(), ['idempotency_key' => 'key-1']);

        $this->assertSame($first->id, $second->id);
        $this->assertSame('key-1', $second->refresh()->metadata['idempotency_key']);
    }

    public function test_cancel_and_illegal_transition(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        app(CancelReservationAction::class)->execute($r);

        $this->assertSame(ReservationStatus::Cancelled, $r->refresh()->status);

        $this->expectException(InvalidArgumentException::class);
        app(ConfirmReservationAction::class)->execute($r->refresh());
    }

    public function test_expire_action_closes_reservation_and_payments(): void
    {
        $r = app(CreateReservationAction::class)->execute($this->payload($this->cabin()));
        $r->update(['expires_at' => now()->subMinute()]);

        $this->assertTrue(app(ExpirePendingReservationAction::class)->execute($r->refresh()));
        $this->assertSame(ReservationStatus::Expired, $r->refresh()->status);
        $this->assertSame(PaymentStatus::Expired, $r->payments->first()->refresh()->status);

        // Second run is a no-op.
        $this->assertFalse(app(ExpirePendingReservationAction::class)->execute($r->refresh()));
    }

    public function test_state_machine_guards(): void
    {
        $this->assertFalse(ReservationStateMachine::can(ReservationStatus::Confirmed, ReservationStatus::Paid));
        $this->assertFalse(PaymentStateMachine::can(PaymentStatus::Paid, PaymentStatus::Failed));
        $this->assertTrue(PaymentStateMachine::can(PaymentStatus::Failed, PaymentStatus::Processing));

        $this->expectException(InvalidArgumentException::class);
        $r = Reservation::factory()->create(['status' => 'confirmed']);
        ReservationStateMachine::transition($r, ReservationStatus::Paid, \App\Enums\ReservationEventType::PaymentSucceeded);
    }

    public function test_booking_code_collision_retries(): void
    {
        $cabin = $this->cabin();
        Str::createRandomStringsUsing(fn () => 'FIXEDCD1');
        $action = app(CreateReservationAction::class);
        $action->execute($this->payload($cabin));

        try {
            $action->execute($this->payload($cabin, [
                'guest_email' => 'other@example.com',
                'check_in' => '2026-12-01',
                'check_out' => '2026-12-03',
            ]));
            $this->fail('Expected booking_code unique violation.');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertStringContainsString('booking_code', $e->getMessage());
        } finally {
            Str::createRandomStringsUsing(null);
        }
    }
}
