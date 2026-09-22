<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Design §9. Full GiST EXCLUDE, no status predicate. */
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE availability_blocks
            ADD CONSTRAINT availability_blocks_no_overlap
            EXCLUDE USING gist (
                cabin_id WITH =,
                daterange(starts_on, ends_on, '[)') WITH &&
            )
            SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('ALTER TABLE availability_blocks DROP CONSTRAINT IF EXISTS availability_blocks_no_overlap');
    }
};
