<?php

namespace Tests\Feature\Api;

use App\Models\Cabin;
use Tests\PgTestCase;

class GuestReservationApiTest extends PgTestCase
{
    /** @return array<string, mixed> */
    private function payload(Cabin $cabin, array $over = []): array
    {
        return array_merge([
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-04',
            'adults' => 2,
            'guest_name' => 'Guest One',
            'guest_email' => 'guest@example.com',
        ], $over);
    }

    public function test_guest_reservation_201_shape(): void
    {
        $r = $this->postJson('/api/v1/reservations', $this->payload(Cabin::factory()->create()));

        $r->assertCreated()
            ->assertJsonStructure(['data' => ['booking_code', 'status', 'total', 'expires_at']])
            ->assertJsonPath('data.status', 'pending_payment');
    }

    public function test_validation_errors_422(): void
    {
        $this->postJson('/api/v1/reservations', [])->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    }

    public function test_invalid_dates_and_capacity_422(): void
    {
        $cabin = Cabin::factory()->create();

        // after:check_in fails at shape level.
        $this->postJson('/api/v1/reservations', $this->payload($cabin, [
            'check_in' => '2026-11-05', 'check_out' => '2026-11-01',
        ]))->assertUnprocessable();

        // Over-capacity passes shape validation, fails in the domain → 422.
        $this->postJson('/api/v1/reservations', $this->payload($cabin, ['adults' => 99]))
            ->assertUnprocessable();
    }

    public function test_unknown_cabin_404(): void
    {
        $this->postJson('/api/v1/reservations', $this->payload(Cabin::factory()->create(), [
            'cabin_id' => '11111111-1111-1111-1111-111111111111',
        ]))->assertUnprocessable();
    }

    public function test_overlap_409_and_touching_boundary_201(): void
    {
        $cabin = Cabin::factory()->create();
        $this->postJson('/api/v1/reservations', $this->payload($cabin))->assertCreated();

        $this->postJson('/api/v1/reservations', $this->payload($cabin, [
            'guest_email' => 'other@example.com',
            'check_in' => '2026-11-02', 'check_out' => '2026-11-05',
        ]))->assertConflict()->assertJson(['message' => 'BOOKING_CONFLICT']);

        // [) touching boundary stays valid.
        $this->postJson('/api/v1/reservations', $this->payload($cabin, [
            'guest_email' => 'third@example.com',
            'check_in' => '2026-11-04', 'check_out' => '2026-11-06',
        ]))->assertCreated();
    }

    public function test_show_and_cancel_with_email_pair(): void
    {
        $cabin = Cabin::factory()->create();
        $code = $this->postJson('/api/v1/reservations', $this->payload($cabin))
            ->assertCreated()->json('data.booking_code');

        $this->getJson("/api/v1/reservations/{$code}?guest_email=guest@example.com")->assertOk()
            ->assertJsonPath('data.booking_code', $code);

        $this->getJson("/api/v1/reservations/{$code}?guest_email=wrong@example.com")->assertNotFound();

        $this->postJson("/api/v1/reservations/{$code}/cancel", ['guest_email' => 'wrong@example.com'])
            ->assertNotFound();

        $this->postJson("/api/v1/reservations/{$code}/cancel", ['guest_email' => 'guest@example.com'])
            ->assertOk()->assertJsonPath('data.status', 'cancelled');
    }
}
