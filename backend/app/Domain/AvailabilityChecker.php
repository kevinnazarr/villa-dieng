<?php

namespace App\Domain;

use App\Enums\ReservationStatus;
use App\Exceptions\BookingConflictException;
use App\Models\AvailabilityBlock;
use App\Models\Reservation;

final class AvailabilityChecker
{
    /** @return list<ReservationStatus> */
    public static function blockingStatuses(): array
    {
        return [
            ReservationStatus::PendingPayment,
            ReservationStatus::Paid,
            ReservationStatus::Confirmed,
        ];
    }

    public static function isAvailable(
        string $cabinId,
        string $checkIn,
        string $checkOut,
        ?string $ignoreReservationId = null,
    ): bool {
        $reservationHit = Reservation::query()
            ->where('cabin_id', $cabinId)
            ->whereIn('status', array_map(
                fn (ReservationStatus $s) => $s->value,
                self::blockingStatuses(),
            ))
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->when($ignoreReservationId !== null, fn ($q) => $q->where('id', '!=', $ignoreReservationId))
            ->exists();

        if ($reservationHit) {
            return false;
        }

        return ! AvailabilityBlock::query()
            ->where('cabin_id', $cabinId)
            ->where('starts_on', '<', $checkOut)
            ->where('ends_on', '>', $checkIn)
            ->exists();
    }

    /** @throws BookingConflictException */
    public static function assertAvailable(
        string $cabinId,
        string $checkIn,
        string $checkOut,
        ?string $ignoreReservationId = null,
    ): void {
        if (! self::isAvailable($cabinId, $checkIn, $checkOut, $ignoreReservationId)) {
            throw new BookingConflictException;
        }
    }
}