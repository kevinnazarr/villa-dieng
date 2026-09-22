<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §4.4. FK CASCADE; partial unique on primary image is pgsql-only. */
    public function up(): void
    {
        Schema::create('cabin_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cabin_id');
            $table->string('path', 500);
            $table->string('alt_text', 255)->nullable();
            $table->smallInteger('sort_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('cabin_id', 'fk_cabin_images_cabin')
                ->references('id')->on('cabins')->onDelete('cascade');
            $table->index(['cabin_id', 'sort_order'], 'idx_cabin_images_cabin_order');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE cabin_images ADD CONSTRAINT chk_cabin_images_order CHECK (sort_order >= 0)');
            DB::statement('CREATE UNIQUE INDEX uq_cabin_primary_image ON cabin_images(cabin_id) WHERE is_primary = TRUE');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cabin_images');
    }
};
