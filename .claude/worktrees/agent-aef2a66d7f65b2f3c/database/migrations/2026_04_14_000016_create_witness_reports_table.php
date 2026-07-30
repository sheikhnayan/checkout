<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('witness_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id')->index();
            $table->unsignedBigInteger('submitted_by_user_id')->nullable()->index();
            $table->string('submitted_via')->default('shared_link');
            $table->string('location_legal_name');
            $table->string('location_dba_name');
            $table->string('location_address');
            $table->date('incident_calendar_date');
            $table->date('date_submitted');
            $table->time('incident_time');
            $table->string('incident_type');
            $table->string('full_name');
            $table->string('address');
            $table->string('phone_number');
            $table->string('participant_type');
            $table->longText('detailed_statement');
            $table->boolean('accepted_esignature')->default(false);
            $table->boolean('opted_out_esignature')->default(false);
            $table->string('digital_signature_name')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('incidents') && Schema::hasColumn('incidents', 'id')) {
            try {
                Schema::table('witness_reports', function (Blueprint $table) {
                    $table->foreign('incident_id')->references('id')->on('incidents')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            try {
                Schema::table('witness_reports', function (Blueprint $table) {
                    $table->foreign('submitted_by_user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('witness_reports');
    }
};
