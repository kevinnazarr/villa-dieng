<?php

namespace App\Actions;

use App\Domain\PaymentStateMachine;
use App\Domain\ReservationStateMachine;
use App\Enums\PaymentStatus;
use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

/** Cancel a reservation (pending_payment/paid/confirmed → cancelled) and expire its open payments. */
final class CancelReservationAction
{
    public function execute(Reservation $reservation, ?\App\Models\User $actor = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $actor) {
            $reservation = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();

            ReservationStateMachine::transition(
                $reservation,
                ReservationStatus::Cancelled,
                ReservationEventType::Cancelled,
                $actor,
            );

            foreach ($reservation->payments as $payment) {
                if (in_array($payment->status, [PaymentStatus::Pending, PaymentStatus::Processing, PaymentStatus::Unknown], true)) {
                    PaymentStateMachine::transition($payment, PaymentStatus::Expired);
                }
            }

            return $reservation->refresh();
        });
    }
}
