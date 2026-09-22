<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'provider' => 'sandbox',
            'provider_reference' => 'sbx-'.Str::uuid(),
            'amount' => 1500000,
            'currency' => 'IDR',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(15),
            'metadata' => [],
        ];
    }
}
