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
        Schema::table('custom_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_invoices', 'package_use_date')) {
                $table->date('package_use_date')->nullable()->after('internal_notes');
            }
            if (!Schema::hasColumn('custom_invoices', 'transportation_arrival_time')) {
                $table->string('transportation_arrival_time', 50)->nullable()->after('package_use_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('custom_invoices', 'transportation_arrival_time')) {
                $table->dropColumn('transportation_arrival_time');
            }
            if (Schema::hasColumn('custom_invoices', 'package_use_date')) {
                $table->dropColumn('package_use_date');
            }
        });
    }
};
