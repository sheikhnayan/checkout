<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_model_event', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_model_id');
            $table->unsignedBigInteger('event_id');
            $table->timestamps();

            $table->index('feed_model_id');
            $table->index('event_id');
            $table->unique(['feed_model_id', 'event_id']);
        });

        if (Schema::hasTable('feed_model_event') && Schema::hasTable('feed_models')) {
            try {
                DB::statement('ALTER TABLE `feed_model_event` ADD CONSTRAINT `feed_model_event_feed_model_id_foreign` FOREIGN KEY (`feed_model_id`) REFERENCES `feed_models`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments.
            }
        }

        if (Schema::hasTable('feed_model_event') && Schema::hasTable('events')) {
            try {
                DB::statement('ALTER TABLE `feed_model_event` ADD CONSTRAINT `feed_model_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_model_event');
    }
};
