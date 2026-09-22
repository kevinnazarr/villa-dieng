<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guest-checkout user fields (design §4.1). Native enum + CITEXT on
     * pgsql; plain strings elsewhere so the sqlite suite keeps passing.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users ALTER COLUMN password DROP NOT NULL');
            DB::statement('ALTER TABLE users ADD COLUMN phone VARCHAR(30)');
            DB::statement("ALTER TABLE users ADD COLUMN locale VARCHAR(5) NOT NULL DEFAULT 'id' CHECK (locale IN ('id', 'en'))");
            DB::statement("ALTER TABLE users ADD COLUMN role user_role NOT NULL DEFAULT 'guest'");
            DB::statement('ALTER TABLE users ALTER COLUMN email TYPE CITEXT');
            DB::statement('CREATE INDEX idx_users_role ON users(role)');

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable();
            $table->string('locale', 5)->default('id');
            $table->string('role')->default('guest');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS idx_users_role');
            DB::statement('ALTER TABLE users ALTER COLUMN email TYPE VARCHAR(255)');
            DB::statement('ALTER TABLE users DROP COLUMN role');
            DB::statement('ALTER TABLE users DROP COLUMN locale');
            DB::statement('ALTER TABLE users DROP COLUMN phone');
            DB::statement('ALTER TABLE users ALTER COLUMN password SET NOT NULL');

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'locale', 'role']);
        });
    }
};
