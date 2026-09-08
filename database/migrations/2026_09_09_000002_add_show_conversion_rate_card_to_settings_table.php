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
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'show_conversion_rate_card')) {
                $table->boolean('show_conversion_rate_card')
                    ->default(true)
                    ->after('show_metric_trends');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_conversion_rate_card')) {
                $table->dropColumn('show_conversion_rate_card');
            }
        });
    }
};
