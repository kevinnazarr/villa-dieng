<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Reservation */
class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_code' => $this->booking_code,
            'cabin_id' => $this->cabin_id,
            'check_in' => $this->check_in,
            'check_out' => $this->check_out,
            'adults' => $this->adults,
            'children' => $this->children,
            'nightly_rate' => $this->nightly_rate,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'currency' => $this->currency,
            'status' => $this->status?->value,
            'guest_name' => $this->guest_name,
            'guest_email' => $this->guest_email,
            'guest_phone' => $this->guest_phone,
            'special_request' => $this->special_request,
            'expires_at' => $this->expires_at,
            'cabin' => new CabinResource($this->whenLoaded('cabin')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'events' => ReservationEventResource::collection($this->whenLoaded('events')),
        ];
    }
}
