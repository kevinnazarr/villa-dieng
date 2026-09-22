<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §5.1. Price snapshot + date/expiry checks; status enum is pgsql-only. */
    public function up(): void
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        Schema::create('reservations', function (Blueprint $table) use ($isPgsql) {
            $table->uuid('id')->primary();
            $table->string('booking_code', 30)->unique();
            $table->uuid('user_id');
            $table->uuid('cabin_id');
            $table->date('check_in');
            $table->date('check_out');
            $table->smallInteger('adults');
            $table->smallInteger('children')->default(0);
            $table->decimal('nightly_rate', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('status')->default('pending_payment');
            $table->string('guest_name', 120);
            $table->string('guest_email', 255);
            $table->string('guest_phone', 30)->nullable();
            $table->text('special_request')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampsTz();

            $table->foreign('user_id', 'fk_reservations_user')
                ->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cabin_id', 'fk_reservations_cabin')
                ->references('id')->on('cabins')->onDelete('restrict');

            $table->index(['cabin_id', 'check_in', 'check_out'], 'idx_reservations_cabin_dates');
            $table->index('status', 'idx_reservations_status');
            $table->index(['user_id', 'created_at'], 'idx_reservations_user_created');
            $table->index('check_in', 'idx_reservations_checkin');
            $table->index('check_out', 'idx_reservations_checkout');
        });

        if ($isPgsql) {
            // Design §7: partial index — expiry scans only touch pending_payment rows.
            DB::statement("CREATE INDEX idx_reservations_pending_expiry ON reservations(expires_at) WHERE status = 'pending_payment'");
        } else {
            Schema::table('reservations', function (Blueprint $table) {
                $table->index('expires_at', 'idx_reservations_pending_expiry');
            });
        }

        if ($isPgsql) {
            // Partial-index predicate on the enum column blocks ALTER TYPE
            // (index functions must be IMMUTABLE): drop, convert, re-create.
            DB::statement('DROP INDEX IF EXISTS idx_reservations_pending_expiry');
            // Text default cannot auto-cast to the enum: drop, convert, re-set.
            DB::statement('ALTER TABLE reservations ALTER COLUMN status DROP DEFAULT');
            DB::statement("ALTER TABLE reservations ALTER COLUMN status TYPE reservation_status USING status::reservation_status");
            DB::statement("ALTER TABLE reservations ALTER COLUMN status SET DEFAULT 'pending_payment'::reservation_status");
            DB::statement("CREATE INDEX idx_reservations_pending_expiry ON reservations(expires_at) WHERE status = 'pending_payment'");
            DB::statement("ALTER TABLE reservations ALTER COLUMN guest_email TYPE CITEXT");
            DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_dates CHECK (check_out > check_in)');
            DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_expiry CHECK (status <> \'pending_payment\' OR expires_at IS NOT NULL)');
            DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_adults CHECK (adults > 0)');
            DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_children CHECK (children >= 0)');
            DB::statement('ALTER TABLE reservations ADD CONSTRAINT chk_reservation_money CHECK (nightly_rate >= 0 AND subtotal >= 0 AND total >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
