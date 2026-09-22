<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PropertyResource;
use App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::where('is_active', true)->orderBy('name')->get();

        return PropertyResource::collection($properties);
    }

    public function show(string $slug)
    {
        $property = Property::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return new PropertyResource($property);
    }
}
