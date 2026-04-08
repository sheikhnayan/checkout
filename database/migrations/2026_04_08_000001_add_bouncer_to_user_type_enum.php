<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("UPDATE users SET user_type = 'admin' WHERE user_type = '' OR user_type IS NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user', 'affiliate', 'entertainer', 'bouncer') NOT NULL DEFAULT 'admin'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("UPDATE users SET user_type = 'website_user' WHERE user_type = 'bouncer'");
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user', 'affiliate', 'entertainer') NOT NULL DEFAULT 'admin'");
    }
};
