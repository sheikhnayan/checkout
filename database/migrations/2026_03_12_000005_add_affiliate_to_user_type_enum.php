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
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user', 'affiliate') NOT NULL DEFAULT 'admin'");

        // Repair any previously truncated rows caused by enum mismatch.
        DB::statement("UPDATE users SET user_type = 'admin' WHERE user_type = '' OR user_type IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Map affiliate users back to website_user before removing enum value.
        DB::statement("UPDATE users SET user_type = 'website_user' WHERE user_type = 'affiliate'");
        DB::statement("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'website_user') NOT NULL DEFAULT 'admin'");
    }
};
