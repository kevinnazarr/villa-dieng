<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §4.3. FK RESTRICT; slug unique per property. */
    public function up(): void
    {
        Schema::create('cabins', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->text('description')->nullable();
            $table->smallInteger('max_adults');
            $table->smallInteger('max_children')->default(0);
            $table->decimal('base_price', 12, 2);
            $table->char('currency', 3)->default('IDR');
            $table->boolean('is_active')->default(true);
            $table->timestampsTz();

            $table->foreign('property_id', 'fk_cabins_property')
                ->references('id')->on('properties')->onDelete('restrict');
            $table->unique(['property_id', 'slug'], 'uq_cabins_property_slug');
            $table->index(['property_id', 'is_active'], 'idx_cabins_property_active');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE cabins ADD CONSTRAINT chk_cabins_adults CHECK (max_adults > 0)');
            DB::statement('ALTER TABLE cabins ADD CONSTRAINT chk_cabins_children CHECK (max_children >= 0)');
            DB::statement('ALTER TABLE cabins ADD CONSTRAINT chk_cabins_price CHECK (base_price >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cabins');
    }
};
