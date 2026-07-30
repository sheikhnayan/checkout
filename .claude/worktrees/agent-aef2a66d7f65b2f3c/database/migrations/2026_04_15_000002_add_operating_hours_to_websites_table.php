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
            if (!Schema::hasColumn('websites', 'operating_days')) {
                $table->json('operating_days')->nullable()->after('transportation_confirmation_text');
            }

            if (!Schema::hasColumn('websites', 'operating_start_time')) {
                $table->string('operating_start_time', 5)->nullable()->after('operating_days');
            }

            if (!Schema::hasColumn('websites', 'operating_end_time')) {
                $table->string('operating_end_time', 5)->nullable()->after('operating_start_time');
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

            if (Schema::hasColumn('websites', 'operating_end_time')) {
                $columnsToDrop[] = 'operating_end_time';
            }

            if (Schema::hasColumn('websites', 'operating_start_time')) {
                $columnsToDrop[] = 'operating_start_time';
            }

            if (Schema::hasColumn('websites', 'operating_days')) {
                $columnsToDrop[] = 'operating_days';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
