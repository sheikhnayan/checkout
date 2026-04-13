<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id')->index();
            $table->string('attachment_type');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->timestamps();
        });

        if (Schema::hasTable('incidents') && Schema::hasColumn('incidents', 'id')) {
            try {
                Schema::table('incident_attachments', function (Blueprint $table) {
                    $table->foreign('incident_id')->references('id')->on('incidents')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_attachments');
    }
};
