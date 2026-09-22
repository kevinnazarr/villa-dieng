<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Cabin */
class CabinResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'max_adults' => $this->max_adults,
            'max_children' => $this->max_children,
            'base_price' => $this->base_price,
            'currency' => $this->currency,
            'is_active' => $this->is_active,
            'images' => CabinImageResource::collection($this->whenLoaded('images')),
            'amenities' => AmenityResource::collection($this->whenLoaded('amenities')),
            'property' => new PropertyResource($this->whenLoaded('property')),
        ];
    }
}
