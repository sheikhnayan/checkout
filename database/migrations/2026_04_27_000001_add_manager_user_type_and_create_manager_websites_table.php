<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Extend user_type enum to include 'manager'
        DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('admin','website_user','affiliate','entertainer','bouncer','manager') NOT NULL DEFAULT 'website_user'");

        // Pivot: which websites a manager user can manage
        Schema::create('manager_websites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('website_id');
            $table->timestamps();
            $table->unique(['user_id', 'website_id']);
            $table->index('user_id');
            $table->index('website_id');
        });

        try {
            DB::statement('ALTER TABLE `manager_websites` ADD CONSTRAINT `manager_websites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            // Resilient across environments
        }

        try {
            DB::statement('ALTER TABLE `manager_websites` ADD CONSTRAINT `manager_websites_website_id_foreign` FOREIGN KEY (`website_id`) REFERENCES `websites`(`id`) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            // Resilient across environments
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('manager_websites');

        DB::statement("ALTER TABLE `users` MODIFY COLUMN `user_type` ENUM('admin','website_user','affiliate','entertainer','bouncer') NOT NULL DEFAULT 'website_user'");
    }
};
