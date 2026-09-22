<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §10. Append-only: no updated_at, no model events that rewrite. */
    public function up(): void
    {
        Schema::create('reservation_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');
            $table->uuid('actor_id')->nullable();
            $table->string('event_type');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->json('metadata')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('reservation_id', 'fk_reservation_events_reservation')
                ->references('id')->on('reservations')->onDelete('cascade');
            $table->foreign('actor_id', 'fk_reservation_events_actor')
                ->references('id')->on('users')->onDelete('set null');

            $table->index(['reservation_id', 'created_at'], 'idx_reservation_events_reservation_created');
            $table->index('event_type', 'idx_reservation_events_type');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE reservation_events ALTER COLUMN event_type TYPE reservation_event_type USING event_type::reservation_event_type');
            DB::statement('ALTER TABLE reservation_events ALTER COLUMN from_status TYPE reservation_status USING from_status::reservation_status');
            DB::statement('ALTER TABLE reservation_events ALTER COLUMN to_status TYPE reservation_status USING to_status::reservation_status');
            DB::statement('ALTER TABLE reservation_events ALTER COLUMN metadata TYPE JSONB USING metadata::jsonb');
            DB::statement("ALTER TABLE reservation_events ALTER COLUMN metadata SET DEFAULT '{}'");
            DB::statement('ALTER TABLE reservation_events ALTER COLUMN metadata SET NOT NULL');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_events');
    }
};
