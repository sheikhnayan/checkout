<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('incident_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('action', 120);
            $table->json('change_summary')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        if (Schema::hasTable('incidents') && Schema::hasColumn('incidents', 'id')) {
            try {
                Schema::table('incident_audit_logs', function (Blueprint $table) {
                    $table->foreign('incident_id')->references('id')->on('incidents')->cascadeOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            try {
                Schema::table('incident_audit_logs', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_audit_logs');
    }
};
