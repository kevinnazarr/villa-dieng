<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Design §8. Own lifecycle; financial rows never cascade. */
    public function up(): void
    {
        $isPgsql = DB::getDriverName() === 'pgsql';

        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('reservation_id');
            $table->string('provider', 50)->default('sandbox');
            $table->string('provider_reference', 100)->nullable()->unique();
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('IDR');
            $table->string('status')->default('pending');
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestampsTz();

            $table->foreign('reservation_id', 'fk_payments_reservation')
                ->references('id')->on('reservations')->onDelete('restrict');

            $table->index('reservation_id', 'idx_payments_reservation');
            $table->index('status', 'idx_payments_status');
            $table->index('provider_reference', 'idx_payments_provider_reference');
        });

        if ($isPgsql) {
            DB::statement('ALTER TABLE payments ALTER COLUMN status DROP DEFAULT');
            DB::statement("ALTER TABLE payments ALTER COLUMN status TYPE payment_status USING status::payment_status");
            DB::statement("ALTER TABLE payments ALTER COLUMN status SET DEFAULT 'pending'::payment_status");
            DB::statement('ALTER TABLE payments ALTER COLUMN metadata TYPE JSONB USING metadata::jsonb');
            DB::statement("ALTER TABLE payments ALTER COLUMN metadata SET DEFAULT '{}'");
            DB::statement('ALTER TABLE payments ALTER COLUMN metadata SET NOT NULL');
            DB::statement('ALTER TABLE payments ADD CONSTRAINT chk_payments_amount CHECK (amount >= 0)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
