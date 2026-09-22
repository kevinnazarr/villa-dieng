<?php

namespace Tests\Feature;

use App\Models\AvailabilityBlock;
use App\Models\Cabin;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Reservation;
use App\Models\ReservationEvent;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Tests\PgTestCase;

/**
 * PostgreSQL domain constraint suite.
 *
 * Verifies the §7 DB design on real PG: EXCLUDE overlaps, FK actions,
 * unique/partial indexes, CITEXT email, check constraints. Fail-hard
 * on missing PG (see PgTestCase) — sqlite cannot prove these.
 */
class DomainConstraintsTest extends PgTestCase
{
    private function makeCabin(): Cabin
    {
        return Cabin::factory()->create();
    }

    private function reserve(Cabin $cabin, string $in, string $out, string $status = 'pending_payment'): Reservation
    {
        return Reservation::factory()->create([
            'cabin_id' => $cabin->id,
            'booking_code' => 'VD-'.Str::upper(Str::random(8)),
            'check_in' => $in,
            'check_out' => $out,
            'status' => $status,
        ]);
    }

    public function test_overlapping_blocking_reservations_rejected(): void
    {
        $cabin = $this->makeCabin();
        $this->reserve($cabin, '2026-11-01', '2026-11-05');

        $this->expectException(QueryException::class);
        $this->reserve($cabin, '2026-11-03', '2026-11-07');
    }

    public function test_touching_boundary_allowed_half_open_semantics(): void
    {
        $cabin = $this->makeCabin();
        $this->reserve($cabin, '2026-11-01', '2026-11-05');

        // check_in == prior check_out: no overlap under [) semantics.
        $next = $this->reserve($cabin, '2026-11-05', '2026-11-08');

        $this->assertNotNull($next->id);
    }

    public function test_overlap_with_cancelled_allowed(): void
    {
        $cabin = $this->makeCabin();
        $this->reserve($cabin, '2026-11-01', '2026-11-05', 'cancelled');

        $r = $this->reserve($cabin, '2026-11-03', '2026-11-07');

        $this->assertNotNull($r->id);
    }

    public function test_availability_block_overlap_rejected(): void
    {
        $cabin = $this->makeCabin();
        $actor = User::factory()->create();
        AvailabilityBlock::create([
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-01',
            'ends_on' => '2026-12-05',
            'reason' => 'maintenance',
            'created_by' => $actor->id,
        ]);

        $this->expectException(QueryException::class);
        AvailabilityBlock::create([
            'cabin_id' => $cabin->id,
            'starts_on' => '2026-12-03',
            'ends_on' => '2026-12-07',
            'created_by' => $actor->id,
        ]);
    }

    public function test_cabin_delete_restricted_with_reservations(): void
    {
        $cabin = $this->makeCabin();
        $this->reserve($cabin, '2026-11-01', '2026-11-05');

        $this->expectException(QueryException::class);
        $cabin->delete();
    }

    public function test_provider_reference_nullable_unique(): void
    {
        $cabin = $this->makeCabin();
        $r = $this->reserve($cabin, '2026-11-01', '2026-11-05');

        // Two NULL references coexist (nullable-unique).
        Payment::create(['reservation_id' => $r->id, 'amount' => 100000, 'currency' => 'IDR']);
        Payment::create(['reservation_id' => $r->id, 'amount' => 100000, 'currency' => 'IDR']);

        Payment::create([
            'reservation_id' => $r->id,
            'provider_reference' => 'PAY-001',
            'amount' => 100000,
            'currency' => 'IDR',
        ]);

        $this->expectException(QueryException::class);
        Payment::create([
            'reservation_id' => $r->id,
            'provider_reference' => 'PAY-001',
            'amount' => 100000,
            'currency' => 'IDR',
        ]);
    }

    public function test_email_citext_case_insensitive_unique(): void
    {
        User::factory()->create(['email' => 'Guest@Example.com']);

        $this->expectException(QueryException::class);
        User::factory()->create(['email' => 'guest@example.com']);
    }

    public function test_cabin_slug_unique_per_property(): void
    {
        $property = Property::factory()->create();
        Cabin::factory()->create(['property_id' => $property->id, 'slug' => 'pine']);

        $this->expectException(QueryException::class);
        Cabin::factory()->create(['property_id' => $property->id, 'slug' => 'pine']);
    }

    public function test_same_slug_allowed_across_properties(): void
    {
        $a = Property::factory()->create();
        $b = Property::factory()->create();
        Cabin::factory()->create(['property_id' => $a->id, 'slug' => 'pine']);
        $cabin = Cabin::factory()->create(['property_id' => $b->id, 'slug' => 'pine']);

        $this->assertNotNull($cabin->id);
    }

    public function test_invalid_dates_rejected_by_check(): void
    {
        $cabin = $this->makeCabin();

        $this->expectException(QueryException::class);
        $this->reserve($cabin, '2026-11-05', '2026-11-01');
    }

    public function test_reservation_events_append_only_shape(): void
    {
        $cabin = $this->makeCabin();
        $r = $this->reserve($cabin, '2026-11-01', '2026-11-05');

        $event = ReservationEvent::create([
            'reservation_id' => $r->id,
            'event_type' => 'created',
            'to_status' => 'pending_payment',
        ]);

        $this->assertSame('created', $event->event_type->value);
        $this->assertEquals(1, $r->events()->count());
    }
}
