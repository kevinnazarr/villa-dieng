<?php

namespace App\Actions;

use App\Domain\PaymentStateMachine;
use App\Domain\ReservationStateMachine;
use App\Enums\PaymentStatus;
use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

final class ExpirePendingReservationAction
{
    public function execute(Reservation $reservation): bool
    {
        return DB::transaction(function () use ($reservation) {
            $reservation = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();

            if ($reservation->status !== ReservationStatus::PendingPayment) {
                return false;
            }

            if ($reservation->expires_at === null || $reservation->expires_at->isFuture()) {
                return false;
            }

            ReservationStateMachine::transition(
                $reservation,
                ReservationStatus::Expired,
                ReservationEventType::Expired,
            );

            foreach ($reservation->payments as $payment) {
                if (in_array($payment->status, [PaymentStatus::Pending, PaymentStatus::Processing, PaymentStatus::Unknown], true)) {
                    PaymentStateMachine::transition($payment, PaymentStatus::Expired);
                }
            }

            return true;
        });
    }
}