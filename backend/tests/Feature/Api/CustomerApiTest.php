<?php

namespace Tests\Feature\Api;

use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\PgTestCase;

class CustomerApiTest extends PgTestCase
{
    private function bookFor(User $user, array $over = []): Reservation
    {
        return Reservation::factory()->create(array_merge([
            'user_id' => $user->id,
            'guest_email' => $user->email,
        ], $over));
    }

    public function test_my_reservations_owner_scoped_paginated(): void
    {
        $me = User::factory()->create();
        $other = User::factory()->create();

        for ($i = 0; $i < 3; $i++) {
            $this->bookFor($me);
        }
        $this->bookFor($other);
        $this->bookFor($me, ['status' => 'cancelled']);

        Sanctum::actingAs($me);

        $r = $this->getJson('/api/v1/my/reservations');
        $r->assertOk();
        // Default pagination 15; all 4 own rows on one page.
        $this->assertSame(4, $r->json('meta.total'));
        foreach ($r->json('data') as $row) {
            $this->assertNotSame($other->id, Reservation::where('booking_code', $row['booking_code'])->first()->user_id);
        }
    }

    public function test_status_filter_works(): void
    {
        $me = User::factory()->create();
        $this->bookFor($me, ['status' => 'pending_payment']);
        $this->bookFor($me, ['status' => 'cancelled']);

        Sanctum::actingAs($me);

        $r = $this->getJson('/api/v1/my/reservations?status=cancelled');
        $r->assertOk();
        $this->assertSame(1, $r->json('meta.total'));
        $this->assertSame('cancelled', $r->json('data.0.status'));
    }

    public function test_requires_authentication(): void
    {
        $this->getJson('/api/v1/my/reservations')->assertUnauthorized();
    }
}
