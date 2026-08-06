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
            if (!Schema::hasColumn('websites', 'show_arrival_time_verbiage')) {
                $table->boolean('show_arrival_time_verbiage')->default(true)->after('transportation_confirmation_text');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            if (Schema::hasColumn('websites', 'show_arrival_time_verbiage')) {
                $table->dropColumn('show_arrival_time_verbiage');
            }
        });
    }
};
