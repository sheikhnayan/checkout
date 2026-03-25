<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_post_id');
            $table->index('feed_post_id');
            $table->string('commenter_name');
            $table->string('commenter_email')->nullable();
            $table->text('body');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        if (Schema::hasTable('feed_comments') && Schema::hasTable('feed_posts')) {
            try {
                DB::statement('ALTER TABLE `feed_comments` ADD CONSTRAINT `feed_comments_feed_post_id_foreign` FOREIGN KEY (`feed_post_id`) REFERENCES `feed_posts`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across live environments with schema engine/order differences.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_comments');
    }
};