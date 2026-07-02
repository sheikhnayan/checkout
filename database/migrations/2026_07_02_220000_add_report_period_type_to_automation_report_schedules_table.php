<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_report_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('automation_report_schedules', 'report_period_type')) {
                $table->string('report_period_type')->default('weekly')->after('frequency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('automation_report_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('automation_report_schedules', 'report_period_type')) {
                $table->dropColumn('report_period_type');
            }
        });
    }
};
