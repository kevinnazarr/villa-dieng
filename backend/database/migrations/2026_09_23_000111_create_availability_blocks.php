<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §9. Table only; overlap exclusion lands in 000112. */
    public function up(): void
    {
        Schema::create('availability_blocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cabin_id');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->text('reason');
            $table->uuid('created_by');
            $table->timestampsTz();

            $table->foreign('cabin_id', 'fk_availability_blocks_cabin')
                ->references('id')->on('cabins')->onDelete('restrict');
            $table->foreign('created_by', 'fk_availability_blocks_creator')
                ->references('id')->on('users')->onDelete('restrict');

            $table->index(['cabin_id', 'starts_on', 'ends_on'], 'idx_availability_blocks_cabin_dates');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE availability_blocks ADD CONSTRAINT chk_availability_block_dates CHECK (ends_on > starts_on)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('availability_blocks');
    }
};
