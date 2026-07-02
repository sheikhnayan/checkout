<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('automation_report_schedules')) {
            return;
        }

        Schema::create('automation_report_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('frequency'); // daily, weekly, monthly, yearly, custom_month_range
            $table->json('website_ids');
            $table->json('email_recipients');
            $table->string('timezone')->default('America/Los_Angeles');
            $table->time('send_time')->default('06:00:00');

            $table->unsignedTinyInteger('weekly_day')->nullable(); // 0=Sun
            $table->unsignedTinyInteger('monthly_day')->nullable(); // 1-31
            $table->unsignedTinyInteger('yearly_month')->nullable(); // 1-12
            $table->unsignedTinyInteger('yearly_day')->nullable(); // 1-31

            $table->date('custom_from_month')->nullable(); // first day of month
            $table->date('custom_to_month')->nullable();   // first day of month

            $table->date('one_time_date')->nullable();
            $table->time('one_time_time')->nullable();

            $table->boolean('is_active')->default(true);
            $table->dateTime('next_run_at')->nullable();
            $table->dateTime('last_run_at')->nullable();
            $table->string('last_run_status')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'next_run_at']);
            $table->index('created_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_report_schedules');
    }
};
