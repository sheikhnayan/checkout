<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('custom_forms')) {
            Schema::create('custom_forms', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
                $table->unsignedBigInteger('updated_by_user_id')->nullable()->index();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->json('website_ids')->nullable();
                $table->boolean('is_active')->default(true);
                $table->json('fields_schema')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('custom_form_submissions')) {
            Schema::create('custom_form_submissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('custom_form_id')->index();
                $table->unsignedBigInteger('website_id')->nullable()->index();
                $table->string('submitter_ip')->nullable();
                $table->text('user_agent')->nullable();
                $table->json('submission_data');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('custom_form_activity_logs')) {
            Schema::create('custom_form_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('custom_form_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('action'); // created, updated, toggled, deleted
                $table->text('changes_summary')->nullable();
                $table->string('ip_address')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_form_activity_logs');
        Schema::dropIfExists('custom_form_submissions');
        Schema::dropIfExists('custom_forms');
    }
};
