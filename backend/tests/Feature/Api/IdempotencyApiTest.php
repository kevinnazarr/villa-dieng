<?php

namespace Tests\Feature\Api;

use App\Models\Cabin;
use App\Models\Payment;
use Tests\PgTestCase;

class IdempotencyApiTest extends PgTestCase
{
    public function test_same_key_no_duplicate_attempts(): void
    {
        $cabin = Cabin::factory()->create();
        $code = $this->postJson('/api/v1/reservations', [
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-04',
            'adults' => 2,
            'guest_name' => 'Guest One',
            'guest_email' => 'guest@example.com',
        ])->assertCreated()->json('data.booking_code');

        $headers = ['Idempotency-Key' => 'key-abc'];

        $first = $this->postJson("/api/v1/reservations/{$code}/payments",
            ['guest_email' => 'guest@example.com'], $headers)->assertOk()->json('data.id');

        $second = $this->postJson("/api/v1/reservations/{$code}/payments",
            ['guest_email' => 'guest@example.com'], $headers)->assertOk()->json('data.id');

        $this->assertSame($first, $second);
        $this->assertSame(1, Payment::count());
    }

    public function test_header_wins_over_body(): void
    {
        $cabin = Cabin::factory()->create();
        $code = $this->postJson('/api/v1/reservations', [
            'cabin_id' => $cabin->id,
            'check_in' => '2026-11-01',
            'check_out' => '2026-11-04',
            'adults' => 2,
            'guest_name' => 'Guest One',
            'guest_email' => 'guest@example.com',
        ])->assertCreated()->json('data.booking_code');

        $id = $this->postJson("/api/v1/reservations/{$code}/payments", [
            'guest_email' => 'guest@example.com',
            'idempotency_key' => 'body-key',
        ], ['Idempotency-Key' => 'header-key'])->assertOk()->json('data.id');

        $this->assertSame('header-key', Payment::find($id)->metadata['idempotency_key']);
    }
}
