<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\AvailabilityBlock;
use App\Models\Cabin;
use App\Models\Reservation;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\PgTestCase;

class AdminApiTest extends PgTestCase
{
    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_non_admin_forbidden(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/admin/reservations')->assertForbidden()
            ->assertJson(['message' => 'Forbidden.']);
    }

    public function test_unauthenticated_401(): void
    {
        $this->getJson('/api/v1/admin/reservations')->assertUnauthorized();
    }

    public function test_list_show_confirm_cancel(): void
    {
        Sanctum::actingAs($this->admin());
        $r = Reservation::factory()->create(['status' => 'pending_payment']);

        $this->getJson('/api/v1/admin/reservations')->assertOk()
            ->assertJsonStructure(['data', 'meta']);

        $this->getJson("/api/v1/admin/reservations/{$r->id}")->assertOk()
            ->assertJsonPath('data.booking_code', $r->booking_code);

        // pending → confirm is illegal → 422 via InvalidArgumentException renderer.
        $this->postJson("/api/v1/admin/reservations/{$r->id}/confirm")->assertUnprocessable();

        $this->postJson("/api/v1/admin/reservations/{$r->id}/cancel")->assertOk()
            ->assertJsonPath('data.status', 'cancelled');
    }

    public function test_admin_confirm_paid(): void
    {
        Sanctum::actingAs($this->admin());
        $r = Reservation::factory()->create(['status' => 'paid']);

        $this->postJson("/api/v1/admin/reservations/{$r->id}/confirm")->assertOk()
            ->assertJsonPath('data.status', 'confirmed');
    }

    public function test_blocks_crud_no_update(): void
    {
        Sanctum::actingAs($this->admin());
        $cabin = Cabin::factory()->create();

        $block = $this->postJson('/api/v1/admin/availability-blocks', [
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-01',
            'ends_on' => '2026-12-05',
            'reason' => 'maintenance',
        ])->assertCreated()->json('data.id');

        $this->getJson('/api/v1/admin/availability-blocks')->assertOk();

        // No update endpoint (wrong method on existing URI → 405).
        $this->putJson("/api/v1/admin/availability-blocks/{$block}", [
            'reason' => 'changed',
        ])->assertMethodNotAllowed();
        $this->patchJson("/api/v1/admin/availability-blocks/{$block}", [
            'reason' => 'changed',
        ])->assertMethodNotAllowed();

        $this->deleteJson("/api/v1/admin/availability-blocks/{$block}")->assertNoContent();
        $this->assertSame(0, AvailabilityBlock::count());

        // Overlapping block → EXCLUDE → 409. Last: RefreshDatabase wraps
        // the test in one transaction and the violation aborts it, so no
        // DB assertions may follow in this test.
        $kept = $this->postJson('/api/v1/admin/availability-blocks', [
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-01',
            'ends_on' => '2026-12-05',
            'reason' => 'kept block',
        ])->assertCreated()->json('data.id');

        $this->postJson('/api/v1/admin/availability-blocks', [
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-03',
            'ends_on' => '2026-12-07',
            'reason' => 'overlap probe',
        ])->assertConflict()->assertJson(['message' => 'BOOKING_CONFLICT']);
    }
}
