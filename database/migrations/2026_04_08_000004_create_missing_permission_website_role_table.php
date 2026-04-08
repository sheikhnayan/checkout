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
        if (!Schema::hasTable('permission_website_role')) {
            Schema::create('permission_website_role', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('website_role_id');
                $table->timestamps();
                $table->unique(['permission_id', 'website_role_id']);
            });
        }

        if (Schema::hasTable('permission_website_role') && Schema::hasTable('permissions')) {
            try {
                DB::statement('ALTER TABLE `permission_website_role` ADD CONSTRAINT `permission_website_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Ignore if FK already exists or cannot be added in current environment.
            }
        }

        if (Schema::hasTable('permission_website_role') && Schema::hasTable('website_roles')) {
            try {
                DB::statement('ALTER TABLE `permission_website_role` ADD CONSTRAINT `permission_website_role_website_role_id_foreign` FOREIGN KEY (`website_role_id`) REFERENCES `website_roles`(`id`) ON DELETE CASCADE');
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
        Schema::dropIfExists('permission_website_role');
    }
};
