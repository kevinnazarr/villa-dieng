<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CabinResource;
use App\Models\Cabin;
use App\Models\Property;

class CabinController extends Controller
{
    private function property(string $slug): Property
    {
        return Property::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function index(string $propertySlug)
    {
        $property = $this->property($propertySlug);

        $cabins = Cabin::where('property_id', $property->id)
            ->where('is_active', true)
            ->with(['images', 'amenities'])
            ->orderBy('name')
            ->get();

        return CabinResource::collection($cabins);
    }

    public function show(string $propertySlug, string $cabinSlug)
    {
        $property = $this->property($propertySlug);

        $cabin = Cabin::where('property_id', $property->id)
            ->where('slug', $cabinSlug)
            ->where('is_active', true)
            ->with(['images', 'amenities', 'property'])
            ->firstOrFail();

        return new CabinResource($cabin);
    }
}
