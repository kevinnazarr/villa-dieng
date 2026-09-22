<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\AvailabilityBlock */
class AvailabilityBlockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cabin_id' => $this->cabin_id,
            'starts_on' => $this->starts_on,
            'ends_on' => $this->ends_on,
            'reason' => $this->reason,
        ];
    }
}
