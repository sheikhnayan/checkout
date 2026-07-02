<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('automation_report_runs')) {
            return;
        }

        Schema::create('automation_report_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_report_schedule_id')
                ->constrained('automation_report_schedules')
                ->cascadeOnDelete();
            $table->foreignId('triggered_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->json('email_recipients')->nullable();
            $table->json('website_ids')->nullable();
            $table->json('report_params')->nullable();
            $table->string('file_path')->nullable();
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['automation_report_schedule_id', 'sent_at']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_report_runs');
    }
};
