<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('status', 30)->default('open')->after('public_witness_token');
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->unsignedBigInteger('status_changed_by_user_id')->nullable()->after('status_changed_at')->index();
        });

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id')) {
            try {
                Schema::table('incidents', function (Blueprint $table) {
                    $table->foreign('status_changed_by_user_id')->references('id')->on('users')->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Keep migration non-blocking on legacy/incompatible VPS schemas.
            }
        }
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            if (Schema::hasColumn('incidents', 'status_changed_by_user_id')) {
                try {
                    $table->dropForeign(['status_changed_by_user_id']);
                } catch (\Throwable $e) {
                    // Foreign key may not exist on some deployments.
                }
                $table->dropColumn('status_changed_by_user_id');
            }

            if (Schema::hasColumn('incidents', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('incidents', 'status_changed_at')) {
                $table->dropColumn('status_changed_at');
            }
        });
    }
};
