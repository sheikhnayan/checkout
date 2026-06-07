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
        // Add is_staff_registration to affiliates table
        if (Schema::hasTable('affiliates') && !Schema::hasColumn('affiliates', 'is_staff_registration')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->boolean('is_staff_registration')->default(false)->after('status');
            });
        }

        // Add is_staff_registration to entertainers table
        if (Schema::hasTable('entertainers') && !Schema::hasColumn('entertainers', 'is_staff_registration')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->boolean('is_staff_registration')->default(false)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('affiliates', 'is_staff_registration')) {
            Schema::table('affiliates', function (Blueprint $table) {
                $table->dropColumn('is_staff_registration');
            });
        }

        if (Schema::hasColumn('entertainers', 'is_staff_registration')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->dropColumn('is_staff_registration');
            });
        }
    }
};
