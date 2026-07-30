<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feed_model_performance_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feed_model_id');
            $table->date('performance_date');
            $table->timestamps();

            $table->index('feed_model_id');
            $table->index('performance_date');
            $table->unique(['feed_model_id', 'performance_date'], 'feed_model_perf_dates_unique');
        });

        if (Schema::hasTable('feed_model_performance_dates') && Schema::hasTable('feed_models')) {
            try {
                DB::statement('ALTER TABLE `feed_model_performance_dates` ADD CONSTRAINT `feed_model_perf_dates_feed_model_id_foreign` FOREIGN KEY (`feed_model_id`) REFERENCES `feed_models`(`id`) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Keep migration resilient across environments.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('feed_model_performance_dates');
    }
};
