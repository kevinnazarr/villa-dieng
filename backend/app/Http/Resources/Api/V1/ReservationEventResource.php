<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ReservationEvent */
class ReservationEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_type' => $this->event_type?->value,
            'from_status' => $this->from_status?->value,
            'to_status' => $this->to_status?->value,
            'created_at' => $this->created_at,
        ];
    }
}
