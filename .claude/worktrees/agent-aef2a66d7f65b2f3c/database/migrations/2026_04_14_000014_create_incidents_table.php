<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id')->index();
            $table->string('public_witness_token')->unique();
            $table->string('location_legal_name');
            $table->string('location_dba_name');
            $table->string('location_address');
            $table->date('incident_calendar_date');
            $table->date('date_submitted');
            $table->time('incident_time');
            $table->string('incident_type')->nullable();
            $table->string('police_report_number')->nullable();
            $table->text('police_officers_badges')->nullable();
            $table->string('reporter_name');
            $table->string('managers_on_duty');
            $table->string('manager_phone')->nullable();
            $table->longText('involved_injured_persons');
            $table->longText('incident_description');
            $table->longText('witnesses_statement');
            $table->text('camera_angles');
            $table->string('camera_timestamp');
            $table->text('cast_members_involved');
            $table->longText('additional_media_notes')->nullable();
            $table->boolean('accepted_esignature')->default(false);
            $table->boolean('opted_out_esignature')->default(false);
            $table->string('digital_signature_name')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable()->index();
            $table->timestamps();
        });

        // Add FK constraints only when referenced tables exist and are compatible.
        if (Schema::hasTable('websites') && Schema::hasColumn('websites', 'id')) {
            try {
                Schema::table('incidents', function (Blueprint $table) {
                    $table->foreign('website_id')->references('id')->on('websites')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            try {
                Schema::table('incidents', function (Blueprint $table) {
                    $table->foreign('created_by_user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
