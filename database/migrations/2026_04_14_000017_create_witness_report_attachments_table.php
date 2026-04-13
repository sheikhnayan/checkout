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
            $table->foreignId('witness_report_id')->constrained('witness_reports')->cascadeOnDelete();
            $table->string('attachment_type')->default('evidence');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('witness_report_attachments');
    }
};
