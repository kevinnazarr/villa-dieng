<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §4.6. Composite PK; cabin side CASCADE, amenity side RESTRICT. */
    public function up(): void
    {
        Schema::create('cabin_amenity', function (Blueprint $table) {
            $table->uuid('cabin_id');
            $table->uuid('amenity_id');
            $table->timestampTz('created_at')->useCurrent();

            $table->primary(['cabin_id', 'amenity_id']);

            $table->foreign('cabin_id', 'fk_cabin_amenity_cabin')
                ->references('id')->on('cabins')->onDelete('cascade');
            $table->foreign('amenity_id', 'fk_cabin_amenity_amenity')
                ->references('id')->on('amenities')->onDelete('restrict');

            $table->index('amenity_id', 'idx_cabin_amenity_amenity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cabin_amenity');
    }
};
