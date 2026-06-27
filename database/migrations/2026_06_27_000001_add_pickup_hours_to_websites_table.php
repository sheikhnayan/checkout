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
            if (!Schema::hasColumn('websites', 'pickup_start_time')) {
                $table->string('pickup_start_time', 5)->nullable()->after('operating_end_time');
            }

            if (!Schema::hasColumn('websites', 'pickup_end_time')) {
                $table->string('pickup_end_time', 5)->nullable()->after('pickup_start_time');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('websites', 'pickup_end_time')) {
                $columnsToDrop[] = 'pickup_end_time';
            }

            if (Schema::hasColumn('websites', 'pickup_start_time')) {
                $columnsToDrop[] = 'pickup_start_time';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
