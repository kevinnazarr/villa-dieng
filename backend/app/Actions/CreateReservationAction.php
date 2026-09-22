<?php

namespace App\Actions;

use App\Domain\AvailabilityChecker;
use App\Enums\ReservationEventType;
use App\Enums\ReservationStatus;
use App\Enums\UserRole;
use App\Exceptions\BookingConflictException;
use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class CreateReservationAction
{
    public const PAYMENT_WINDOW_MINUTES = 15;

    private const BOOKING_CODE_ATTEMPTS = 5;

    /** @param array<string, mixed> $data @throws BookingConflictException */
    public function execute(array $data): Reservation
    {
        $checkIn = $data['check_in'];
        $checkOut = $data['check_out'];

        if ($checkOut <= $checkIn) {
            throw new InvalidArgumentException('check_out must be after check_in.');
        }

        $email = strtolower(trim($data['guest_email']));

        $user = User::where('email', $email)->first() ?? User::create([
            'name' => $data['guest_name'],
            'email' => $email,
            'phone' => $data['guest_phone'] ?? null,
            'role' => UserRole::Guest,
        ]);

        $attempt = 0;

        while (true) {
            try {
                return DB::transaction(function () use ($data, $checkIn, $checkOut, $email, $user) {
                    $cabin = Cabin::whereKey($data['cabin_id'])->lockForUpdate()->firstOrFail();

                    if (! $cabin->is_active) {
                        throw new BookingConflictException('Cabin is not active.');
                    }

                    if ($data['adults'] > $cabin->max_adults || ($data['children'] ?? 0) > $cabin->max_children) {
                        throw new InvalidArgumentException('Guest count exceeds cabin capacity.');
                    }

                    AvailabilityChecker::assertAvailable($cabin->id, $checkIn, $checkOut);

                    $nights = (int) now()->parse($checkIn)->diffInDays(now()->parse($checkOut));
                    $subtotal = number_format((float) $cabin->base_price * $nights, 2, '.', '');

                    $reservation = Reservation::create([
                        'booking_code' => 'VD-'.Str::upper(Str::random(8)),
                        'user_id' => $user->id,
                        'cabin_id' => $cabin->id,
                        'check_in' => $checkIn,
                        'check_out' => $checkOut,
                        'adults' => $data['adults'],
                        'children' => $data['children'] ?? 0,
                        'nightly_rate' => $cabin->base_price,
                        'subtotal' => $subtotal,
                        'total' => $subtotal,
                        'currency' => $cabin->currency,
                        'status' => ReservationStatus::PendingPayment,
                        'guest_name' => $data['guest_name'],
                        'guest_email' => $email,
                        'guest_phone' => $data['guest_phone'] ?? null,
                        'special_request' => $data['special_request'] ?? null,
                        'expires_at' => now()->addMinutes(self::PAYMENT_WINDOW_MINUTES),
                    ]);

                    $reservation->payments()->create([
                        'provider' => 'sandbox',
                        'provider_reference' => 'sbx-'.Str::uuid(),
                        'amount' => $subtotal,
                        'currency' => $cabin->currency,
                        'status' => 'pending',
                        'expires_at' => $reservation->expires_at,
                        'metadata' => isset($data['idempotency_key'])
                            ? ['idempotency_key' => $data['idempotency_key']]
                            : [],
                    ]);

                    $reservation->events()->create([
                        'event_type' => ReservationEventType::Created,
                        'from_status' => null,
                        'to_status' => ReservationStatus::PendingPayment,
                        'metadata' => [],
                    ]);

                    return $reservation;
                });
            } catch (QueryException $e) {
                if (BookingConflictException::isExclusionViolation($e)) {
                    throw BookingConflictException::fromExclusionViolation($e);
                }

                if (str_contains($e->getMessage(), 'booking_code') && ++$attempt < self::BOOKING_CODE_ATTEMPTS) {
                    continue;
                }

                throw $e;
            }
        }
    }
}