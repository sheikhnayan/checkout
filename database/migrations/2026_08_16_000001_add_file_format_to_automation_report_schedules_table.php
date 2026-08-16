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
        if (Schema::hasTable('automation_report_schedules')) {
            Schema::table('automation_report_schedules', function (Blueprint $table) {
                if (!Schema::hasColumn('automation_report_schedules', 'file_format')) {
                    $table->string('file_format', 20)->default('pdf')->after('hostname_filter');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('automation_report_schedules')) {
            Schema::table('automation_report_schedules', function (Blueprint $table) {
                if (Schema::hasColumn('automation_report_schedules', 'file_format')) {
                    $table->dropColumn('file_format');
                }
            });
        }
    }
};
