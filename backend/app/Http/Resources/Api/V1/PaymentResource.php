<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Payment view. Never exposes metadata (idempotency keys stay opaque).
 *
 * @mixin \App\Models\Payment
 */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_id' => $this->reservation_id,
            'provider' => $this->provider,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'status' => $this->status?->value,
            'paid_at' => $this->paid_at,
            'expires_at' => $this->expires_at,
        ];
    }
}
