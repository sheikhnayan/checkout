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
            $table->foreignId('incident_id')->constrained('incidents')->cascadeOnDelete();
            $table->foreignId('submitted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('witness_reports');
    }
};
