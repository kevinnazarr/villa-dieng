<?php

namespace App\Actions;

use App\Domain\PaymentStateMachine;
use App\Domain\ReservationStateMachine;
use App\Enums\PaymentStatus;
use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\SandboxPaymentGateway;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class ProcessPaymentAction
{
    public function __construct(private SandboxPaymentGateway $gateway) {}

    public function execute(Payment $payment, string $scenario = SandboxPaymentGateway::SCENARIO_SUCCESS): Payment
    {
        $payment->refresh();

        if (! in_array($payment->status, [PaymentStatus::Pending, PaymentStatus::Failed], true)) {
            throw new InvalidArgumentException('Only pending or failed payments can be processed.');
        }

        DB::transaction(function () use ($payment) {
            $locked = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            PaymentStateMachine::transition($locked, PaymentStatus::Processing);
        });

        $outcome = $this->gateway->charge(
            (string) $payment->provider_reference,
            (string) $payment->amount,
            $scenario,
        );

        DB::transaction(function () use ($payment, $outcome) {
            $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
            $reservation = Reservation::whereKey($payment->reservation_id)->lockForUpdate()->firstOrFail();

            if ($outcome === 'paid') {
                PaymentStateMachine::transition($payment, PaymentStatus::Paid);

                $reservation->events()->create([
                    'event_type' => ReservationEventType::PaymentSucceeded,
                    'from_status' => $reservation->status,
                    'to_status' => null,
                    'metadata' => ['payment_id' => $payment->id],
                ]);

                if ($reservation->status === ReservationStatus::PendingPayment) {
                    ReservationStateMachine::transition(
                        $reservation,
                        ReservationStatus::Paid,
                        ReservationEventType::PaymentSucceeded,
                        null,
                        ['payment_id' => $payment->id],
                    );
                    ReservationStateMachine::transition(
                        $reservation,
                        ReservationStatus::Confirmed,
                        ReservationEventType::Confirmed,
                        null,
                        ['payment_id' => $payment->id],
                    );
                }
            } elseif ($outcome === 'failed') {
                PaymentStateMachine::transition($payment, PaymentStatus::Failed);

                $reservation->events()->create([
                    'event_type' => ReservationEventType::PaymentFailed,
                    'from_status' => $reservation->status,
                    'to_status' => null,
                    'metadata' => ['payment_id' => $payment->id],
                ]);
            } else {
                PaymentStateMachine::transition($payment, PaymentStatus::Unknown);

                $reservation->events()->create([
                    'event_type' => ReservationEventType::PaymentFailed,
                    'from_status' => $reservation->status,
                    'to_status' => null,
                    'metadata' => ['payment_id' => $payment->id, 'outcome' => 'unknown'],
                ]);
            }
        });

        return $payment->refresh();
    }
}