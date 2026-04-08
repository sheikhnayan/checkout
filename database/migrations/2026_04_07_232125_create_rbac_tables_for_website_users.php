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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->string('module')->index();
            $table->text('description')->nullable();
            $table->boolean('is_super_admin_only')->default(false);
            $table->timestamps();
        });

        Schema::create('website_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->nullable()->index();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_website_admin')->default(false);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->unique(['website_id', 'slug']);
        });

        if (Schema::hasTable('website_roles') && Schema::hasTable('websites')) {
            try {
                DB::statement('ALTER TABLE `website_roles` ADD CONSTRAINT `website_roles_website_id_foreign` FOREIGN KEY (`website_id`) REFERENCES `websites`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments with different migration order/state.
            }
        }

        Schema::create('permission_website_role', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('website_role_id');
            $table->timestamps();
            $table->unique(['permission_id', 'website_role_id']);
        });

        if (Schema::hasTable('permission_website_role') && Schema::hasTable('permissions')) {
            try {
                DB::statement('ALTER TABLE `permission_website_role` ADD CONSTRAINT `permission_website_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments with different migration order/state.
            }
        }

        if (Schema::hasTable('permission_website_role') && Schema::hasTable('website_roles')) {
            try {
                DB::statement('ALTER TABLE `permission_website_role` ADD CONSTRAINT `permission_website_role_website_role_id_foreign` FOREIGN KEY (`website_role_id`) REFERENCES `website_roles`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments with different migration order/state.
            }
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'website_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('website_role_id')->nullable()->after('website_id');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'website_role_id') && Schema::hasTable('website_roles')) {
            try {
                DB::statement('ALTER TABLE `users` ADD CONSTRAINT `users_website_role_id_foreign` FOREIGN KEY (`website_role_id`) REFERENCES `website_roles`(`id`) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments with different migration order/state.
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
                // Foreign key may not exist in some environments.
            }

            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('website_role_id');
            });
        }

        Schema::dropIfExists('permission_website_role');
        Schema::dropIfExists('website_roles');
        Schema::dropIfExists('permissions');
    }
};
