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
        Schema::table('nr_nightly_reports', function (Blueprint $table) {
            $table->string('additional_recipient')->nullable();
            $table->text('incident_notes')->nullable();
            $table->text('nightly_checklists')->nullable();
            $table->string('browser')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('unique_id')->nullable();
            $table->string('submission_location')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nr_nightly_reports', function (Blueprint $table) {
            $table->dropColumn([
                'additional_recipient',
                'incident_notes',
                'nightly_checklists',
                'browser',
                'ip_address',
                'unique_id',
                'submission_location',
            ]);
        });
    }
};
