<?php

namespace App\Actions;

use App\Enums\PaymentStatus;
use App\Enums\ReservationEventType;
use App\Models\Reservation;
use Illuminate\Support\Str;
use InvalidArgumentException;

final class InitiatePaymentAction
{
    public function execute(Reservation $reservation, array $metadata = []): \App\Models\Payment
    {
        $reservation->refresh();

        if ($reservation->status->value !== 'pending_payment') {
            throw new InvalidArgumentException('Only pending_payment reservations can initiate payment.');
        }

        $open = $reservation->payments()
            ->whereIn('status', [
                PaymentStatus::Pending->value,
                PaymentStatus::Processing->value,
                PaymentStatus::Unknown->value,
            ])
            ->latest()
            ->first();

        if ($open) {
            if (isset($metadata['idempotency_key'])) {
                $meta = $open->metadata ?? [];
                $meta['idempotency_key'] = $metadata['idempotency_key'];
                $open->metadata = $meta;
                $open->save();
            }

            return $open;
        }

        $payment = $reservation->payments()->create([
            'provider' => 'sandbox',
            'provider_reference' => 'sbx-'.Str::uuid(),
            'amount' => $reservation->total,
            'currency' => $reservation->currency,
            'status' => PaymentStatus::Pending,
            'expires_at' => $reservation->expires_at,
            'metadata' => $metadata,
        ]);

        $reservation->events()->create([
            'event_type' => ReservationEventType::PaymentInitiated,
            'from_status' => $reservation->status,
            'to_status' => null,
            'metadata' => ['payment_id' => $payment->id],
        ]);

        return $payment;
    }
}