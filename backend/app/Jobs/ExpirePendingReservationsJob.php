<?php

namespace App\Jobs;

use App\Actions\ExpirePendingReservationAction;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Per-minute sweep: expire pending_payment reservations past their
 * 15-minute window. Per-row lock + re-check inside the action; skip-locked
 * so concurrent workers never block each other.
 */
class ExpirePendingReservationsJob implements ShouldQueue
{
    use Queueable;

    public function handle(ExpirePendingReservationAction $expire): void
    {
        Reservation::query()
            ->where('status', 'pending_payment')
            ->where('expires_at', '<=', now())
            ->orderBy('expires_at')
            ->lock('for update skip locked')
            ->chunkById(100, function ($reservations) use ($expire): void {
                foreach ($reservations as $reservation) {
                    $expire->execute($reservation);
                }
            });
    }
}
