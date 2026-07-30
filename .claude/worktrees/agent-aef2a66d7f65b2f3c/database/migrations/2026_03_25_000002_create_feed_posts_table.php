<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->index('website_id');
            $table->unsignedBigInteger('feed_model_id');
            $table->index('feed_model_id');
            $table->text('caption')->nullable();
            $table->json('images')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('feed_posts') && Schema::hasTable('websites')) {
            try {
                DB::statement('ALTER TABLE `feed_posts` ADD CONSTRAINT `feed_posts_website_id_foreign` FOREIGN KEY (`website_id`) REFERENCES `websites`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across live environments with schema engine/order differences.
            }
        }

        if (Schema::hasTable('feed_posts') && Schema::hasTable('feed_models')) {
            try {
                DB::statement('ALTER TABLE `feed_posts` ADD CONSTRAINT `feed_posts_feed_model_id_foreign` FOREIGN KEY (`feed_model_id`) REFERENCES `feed_models`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across live environments with schema engine/order differences.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_posts');
    }
};