<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Native PostgreSQL enums from the approved technical design (§3).
     * No-op on other drivers so the sqlite foundation suite keeps passing.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach ([
            "CREATE TYPE user_role AS ENUM ('guest', 'admin')",
            "CREATE TYPE reservation_status AS ENUM ('pending_payment', 'paid', 'confirmed', 'expired', 'cancelled')",
            "CREATE TYPE payment_status AS ENUM ('pending', 'processing', 'paid', 'failed', 'unknown', 'expired')",
            "CREATE TYPE reservation_event_type AS ENUM ('created', 'payment_initiated', 'payment_succeeded', 'payment_failed', 'confirmed', 'expired', 'cancelled', 'availability_blocked', 'availability_unblocked')",
        ] as $createType) {
            DB::statement("DO \$\$ BEGIN {$createType}; EXCEPTION WHEN duplicate_object THEN NULL; END \$\$;");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP TYPE IF EXISTS reservation_event_type');
        DB::statement('DROP TYPE IF EXISTS payment_status');
        DB::statement('DROP TYPE IF EXISTS reservation_status');
        DB::statement('DROP TYPE IF EXISTS user_role');
    }
};