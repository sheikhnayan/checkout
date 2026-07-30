<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'website_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('website_role_id')->nullable();
                $table->index('website_role_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'website_role_id') && Schema::hasTable('website_roles')) {
            try {
                DB::statement('ALTER TABLE `users` ADD CONSTRAINT `users_website_role_id_foreign` FOREIGN KEY (`website_role_id`) REFERENCES `website_roles`(`id`) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                // Ignore if FK already exists or cannot be added in current environment.
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'website_role_id')) {
            try {
                DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_website_role_id_foreign`');
            } catch (\Throwable $e) {
                // Ignore if FK does not exist.
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['website_role_id']);
                $table->dropColumn('website_role_id');
            });
        }
    }
};
