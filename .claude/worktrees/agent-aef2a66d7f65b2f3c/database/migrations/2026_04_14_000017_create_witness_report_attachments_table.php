<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('witness_report_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('witness_report_id')->index();
            $table->string('attachment_type')->default('evidence');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });

        if (Schema::hasTable('witness_reports') && Schema::hasColumn('witness_reports', 'id')) {
            try {
                Schema::table('witness_report_attachments', function (Blueprint $table) {
                    $table->foreign('witness_report_id')->references('id')->on('witness_reports')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('witness_report_attachments');
    }
};
