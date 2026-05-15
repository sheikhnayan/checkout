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
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'google_analytics_id')) {
                $table->string('google_analytics_id', 64)->nullable()->after('domain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            if (Schema::hasColumn('websites', 'google_analytics_id')) {
                $table->dropColumn('google_analytics_id');
            }
        });
    }
};
