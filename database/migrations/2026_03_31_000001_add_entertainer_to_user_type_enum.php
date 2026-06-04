<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user', 'affiliate', 'entertainer') NOT NULL DEFAULT 'admin'");
        DB::statement("UPDATE users SET user_type = 'admin' WHERE user_type = '' OR user_type IS NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE users SET user_type = 'website_user' WHERE user_type = 'entertainer'");
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user', 'affiliate') NOT NULL DEFAULT 'admin'");
    }
};
