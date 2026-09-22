<?php

namespace Database\Factories;

use App\Models\Cabin;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cabin>
 */
class CabinFactory extends Factory
{
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'name' => fake()->words(2, true).' Cabin',
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'max_adults' => 2,
            'max_children' => 2,
            'base_price' => 750000,
            'currency' => 'IDR',
            'is_active' => true,
        ];
    }
}
