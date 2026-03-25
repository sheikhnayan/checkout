<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_models', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->index('website_id');
            $table->string('name');
            $table->string('profile_image')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('feed_models') && Schema::hasTable('websites')) {
            try {
                DB::statement('ALTER TABLE `feed_models` ADD CONSTRAINT `feed_models_website_id_foreign` FOREIGN KEY (`website_id`) REFERENCES `websites`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across live environments with schema engine/order differences.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_models');
    }
};