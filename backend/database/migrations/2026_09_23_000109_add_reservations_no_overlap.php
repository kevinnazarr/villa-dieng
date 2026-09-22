<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Design §6. Partial GiST EXCLUDE: blocking statuses only, [) semantics. */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE reservations
            ADD CONSTRAINT reservations_no_overlap
            EXCLUDE USING gist (
                cabin_id WITH =,
                daterange(check_in, check_out, '[)') WITH &&
            )
            WHERE (
                status IN ('pending_payment', 'paid', 'confirmed')
            )
            SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE reservations DROP CONSTRAINT IF EXISTS reservations_no_overlap');
    }
};
