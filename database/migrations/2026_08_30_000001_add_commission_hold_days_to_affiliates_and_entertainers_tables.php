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
        Schema::table('affiliates', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliates', 'commission_hold_days')) {
                $table->unsignedSmallInteger('commission_hold_days')->nullable()->after('default_commission_percentage');
            }
        });

        Schema::table('entertainers', function (Blueprint $table) {
            if (!Schema::hasColumn('entertainers', 'commission_hold_days')) {
                $table->unsignedSmallInteger('commission_hold_days')->nullable()->after('default_commission_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            if (Schema::hasColumn('affiliates', 'commission_hold_days')) {
                $table->dropColumn('commission_hold_days');
            }
        });

        Schema::table('entertainers', function (Blueprint $table) {
            if (Schema::hasColumn('entertainers', 'commission_hold_days')) {
                $table->dropColumn('commission_hold_days');
            }
        });
    }
};
