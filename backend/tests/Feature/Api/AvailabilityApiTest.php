<?php

namespace Tests\Feature\Api;

use App\Models\AvailabilityBlock;
use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use Tests\PgTestCase;

class AvailabilityApiTest extends PgTestCase
{
    public function test_available_dates_return_true(): void
    {
        $cabin = Cabin::factory()->create();

        $r = $this->getJson("/api/v1/availability?cabin_id={$cabin->id}&check_in=2026-11-01&check_out=2026-11-04");

        $r->assertOk()->assertJsonPath('data.available', true);
    }

    public function test_booked_dates_return_false(): void
    {
        $cabin = Cabin::factory()->create();
        Reservation::factory()->create([
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-05',
            'status' => 'confirmed',
        ]);

        $r = $this->getJson("/api/v1/availability?cabin_id={$cabin->id}&check_in=2026-11-03&check_out=2026-11-07");

        $r->assertOk()->assertJsonPath('data.available', false);
    }

    public function test_blocked_dates_return_false(): void
    {
        $cabin = Cabin::factory()->create();
        $actor = User::factory()->create();
        AvailabilityBlock::create([
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-01',
            'ends_on' => '2026-12-05',
            'reason' => 'maintenance',
            'created_by' => $actor->id,
        ]);

        $r = $this->getJson("/api/v1/availability?cabin_id={$cabin->id}&check_in=2026-12-02&check_out=2026-12-04");

        $r->assertOk()->assertJsonPath('data.available', false);
    }

    public function test_missing_and_invalid_params_422(): void
    {
        $cabin = Cabin::factory()->create();

        $this->getJson("/api/v1/availability?cabin_id={$cabin->id}")->assertUnprocessable();
        $this->getJson('/api/v1/availability?cabin_id=not-a-uuid&check_in=2026-11-01&check_out=2026-11-04')
            ->assertUnprocessable();
        $this->getJson("/api/v1/availability?cabin_id={$cabin->id}&check_in=2026-11-05&check_out=2026-11-01")
            ->assertUnprocessable();
    }
}
