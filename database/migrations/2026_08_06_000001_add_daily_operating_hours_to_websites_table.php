<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'daily_operating_hours')) {
                $table->json('daily_operating_hours')->nullable()->after('pickup_end_time');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            if (Schema::hasColumn('websites', 'daily_operating_hours')) {
                $table->dropColumn('daily_operating_hours');
            }
        });
    }
};
