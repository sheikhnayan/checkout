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
        if (Schema::hasTable('custom_invoices') && !Schema::hasColumn('custom_invoices', 'processing_fee')) {
            Schema::table('custom_invoices', function (Blueprint $table) {
                $table->decimal('processing_fee', 10, 2)->default(0)->after('service_charge');
                $table->string('processing_fee_name')->nullable()->after('processing_fee');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('custom_invoices') && Schema::hasColumn('custom_invoices', 'processing_fee')) {
            Schema::table('custom_invoices', function (Blueprint $table) {
                $table->dropColumn(['processing_fee', 'processing_fee_name']);
            });
        }
    }
};
