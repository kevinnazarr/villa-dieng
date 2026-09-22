<?php

namespace App\Domain;

use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Models\Reservation;
use App\Models\User;
use InvalidArgumentException;

/**
 * Reservation lifecycle (backend AGENTS.md). Every status change goes
 * through here; each transition appends a reservation event.
 */
final class ReservationStateMachine
{
    /** @return array<string, list<string>> */
    public static function allowed(): array
    {
        return [
            ReservationStatus::PendingPayment->value => [
                ReservationStatus::Paid->value,
                ReservationStatus::Expired->value,
                ReservationStatus::Cancelled->value,
            ],
            ReservationStatus::Paid->value => [
                ReservationStatus::Confirmed->value,
                ReservationStatus::Cancelled->value,
            ],
            ReservationStatus::Confirmed->value => [
                ReservationStatus::Cancelled->value,
            ],
            ReservationStatus::Expired->value => [],
            ReservationStatus::Cancelled->value => [],
        ];
    }

    public static function can(ReservationStatus $from, ReservationStatus $to): bool
    {
        return in_array($to->value, self::allowed()[$from->value] ?? [], true);
    }

    public static function transition(
        Reservation $reservation,
        ReservationStatus $to,
        ReservationEventType $event,
        ?User $actor = null,
        array $metadata = [],
    ): Reservation {
        $from = $reservation->status;

        if (! self::can($from, $to)) {
            throw new InvalidArgumentException(
                "Illegal reservation transition from {$from->value} to {$to->value}."
            );
        }

        $reservation->status = $to;
        $reservation->save();

        $reservation->events()->create([
            'actor_id' => $actor?->id,
            'event_type' => $event,
            'from_status' => $from,
            'to_status' => $to,
            'metadata' => $metadata,
        ]);

        return $reservation;
    }
}
