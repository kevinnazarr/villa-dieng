<?php

namespace App\Actions;

use App\Domain\ReservationStateMachine;
use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

/** Confirm a paid reservation (paid → confirmed). Payment success auto-confirms; this covers manual/admin flows. */
final class ConfirmReservationAction
{
    public function execute(Reservation $reservation, ?\App\Models\User $actor = null): Reservation
    {
        return DB::transaction(function () use ($reservation, $actor) {
            $reservation = Reservation::whereKey($reservation->id)->lockForUpdate()->firstOrFail();

            ReservationStateMachine::transition(
                $reservation,
                ReservationStatus::Confirmed,
                ReservationEventType::Confirmed,
                $actor,
            );

            return $reservation->refresh();
        });
    }
}
