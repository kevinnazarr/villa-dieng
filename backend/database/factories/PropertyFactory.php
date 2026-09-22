<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Villa',
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'is_active' => true,
        ];
    }
}
