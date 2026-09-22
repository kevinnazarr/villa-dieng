<?php

namespace Database\Factories;

use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    public function definition(): array
    {
        $checkIn = fake()->dateTimeBetween('+1 week', '+2 weeks');

        return [
            'user_id' => User::factory(),
            'cabin_id' => Cabin::factory(),
            'booking_code' => 'VD-'.Str::upper(Str::random(8)),
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => (clone $checkIn)->modify('+2 days')->format('Y-m-d'),
            'adults' => 2,
            'children' => 0,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'guest_phone' => fake()->phoneNumber(),
            'nightly_rate' => 750000,
            'subtotal' => 1500000,
            'total' => 1500000,
            'currency' => 'IDR',
            'status' => 'pending_payment',
            // Design §9: pending reservations must carry an expiry timestamp.
            'expires_at' => now()->addHour(),
        ];
    }
}
