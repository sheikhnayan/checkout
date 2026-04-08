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
        Schema::create('job_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->index();
            $table->unsignedBigInteger('posted_by_user_id')->nullable()->index();
            $table->string('job_type', 30); // entertainer or employee
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('compensation')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('skills')->nullable();
            $table->json('traits')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_archived')->default(false);
            $table->timestamps();
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_post_id')->index();
            $table->unsignedBigInteger('website_id')->index();
            $table->string('application_type', 30); // entertainer or employee
            $table->string('legal_first_name')->nullable();
            $table->string('legal_last_name')->nullable();
            $table->string('display_first_name')->nullable();
            $table->string('display_last_name')->nullable();
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('preferred_contact_method')->nullable();
            $table->string('status')->default('new');
            $table->json('social_handles')->nullable();
            $table->json('traits')->nullable();
            $table->json('skills')->nullable();
            $table->json('availability')->nullable();
            $table->json('positions')->nullable();
            $table->json('employment_history')->nullable();
            $table->json('education')->nullable();
            $table->json('attachments')->nullable();
            $table->longText('additional_notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('job_preference_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->index();
            $table->string('name');
            $table->string('email')->index();
            $table->string('phone')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('preferred_role')->nullable();
            $table->json('availability')->nullable();
            $table->json('social_handles')->nullable();
            $table->json('experience')->nullable();
            $table->json('attachments')->nullable();
            $table->longText('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_preference_requests');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_posts');
    }
};
