<?php

namespace Tests\Feature\Api;

use App\Models\Cabin;
use App\Models\Payment;
use Tests\PgTestCase;

class PaymentApiTest extends PgTestCase
{
    private function book(array $over = []): string
    {
        $cabin = Cabin::factory()->create();

        return $this->postJson('/api/v1/reservations', array_merge([
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-04',
            'adults' => 2,
            'guest_name' => 'Guest One',
            'guest_email' => 'guest@example.com',
        ], $over))->assertCreated()->json('data.booking_code');
    }

    public function test_initiate_reuses_open_payment(): void
    {
        $code = $this->book();

        $first = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $second = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->assertSame($first, $second);
        $this->assertSame(1, Payment::count());
    }

    public function test_success_fail_timeout_paths(): void
    {
        $code = $this->book();
        $paymentId = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'guest@example.com', 'scenario' => 'success',
        ])->assertOk()->assertJsonPath('data.status', 'paid');

        $this->getJson("/api/v1/reservations/{$code}?guest_email=guest@example.com")
            ->assertOk()->assertJsonPath('data.status', 'confirmed');

        // Fail path on a fresh booking.
        $code2 = $this->book(['guest_email' => 'second@example.com']);
        $pay2 = $this->postJson("/api/v1/reservations/{$code2}/payments", ['guest_email' => 'second@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$pay2}/process", [
            'guest_email' => 'second@example.com', 'scenario' => 'fail',
        ])->assertOk()->assertJsonPath('data.status', 'failed');

        $this->getJson("/api/v1/reservations/{$code2}?guest_email=second@example.com")
            ->assertOk()->assertJsonPath('data.status', 'pending_payment');

        // Timeout path.
        $code3 = $this->book(['guest_email' => 'third@example.com']);
        $pay3 = $this->postJson("/api/v1/reservations/{$code3}/payments", ['guest_email' => 'third@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$pay3}/process", [
            'guest_email' => 'third@example.com', 'scenario' => 'timeout',
        ])->assertOk()->assertJsonPath('data.status', 'unknown');
    }

    public function test_invalid_scenario_422(): void
    {
        $code = $this->book();
        $paymentId = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'guest@example.com', 'scenario' => 'jackpot',
        ])->assertUnprocessable();
    }

    public function test_production_rejects_scenario(): void
    {
        // environment() reads the boot-time binding, not live config.
        app()->detectEnvironment(fn () => 'production');

        $code = $this->book();
        $paymentId = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'guest@example.com', 'scenario' => 'success',
        ])->assertUnprocessable();
    }

    public function test_payment_never_exposes_metadata(): void
    {
        $code = $this->book();
        $r = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk();

        $this->assertArrayNotHasKey('metadata', $r->json('data'));
        $this->assertArrayNotHasKey('provider_reference', $r->json('data'));
    }

    public function test_wrong_email_cannot_process(): void
    {
        $code = $this->book();
        $paymentId = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'intruder@example.com', 'scenario' => 'success',
        ])->assertNotFound();
    }

    public function test_process_non_pending_payment_422(): void
    {
        $code = $this->book();
        $paymentId = $this->postJson("/api/v1/reservations/{$code}/payments", ['guest_email' => 'guest@example.com'])
            ->assertOk()->json('data.id');

        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'guest@example.com', 'scenario' => 'success',
        ])->assertOk();

        // Already paid — domain rejects re-processing → 422.
        $this->postJson("/api/v1/payments/{$paymentId}/process", [
            'guest_email' => 'guest@example.com', 'scenario' => 'success',
        ])->assertUnprocessable();
    }
}
