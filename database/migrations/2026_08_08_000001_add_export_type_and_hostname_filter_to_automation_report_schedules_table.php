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
                if (!Schema::hasColumn('automation_report_schedules', 'export_type')) {
                    $table->string('export_type', 40)->default('executive')->after('report_period_type');
                }
                if (!Schema::hasColumn('automation_report_schedules', 'hostname_filter')) {
                    $table->string('hostname_filter', 40)->default('all')->after('export_type');
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
                $table->dropColumn(['export_type', 'hostname_filter']);
            });
        }
    }
};
