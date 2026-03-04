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
            // Add detailed fee breakdown fields
            $table->decimal('gratuity', 10, 2)->default(0)->after('subtotal');
            $table->string('gratuity_name')->nullable()->after('gratuity');
            $table->decimal('refundable', 10, 2)->default(0)->after('gratuity_name');
            $table->string('refundable_name')->nullable()->after('refundable');
            $table->decimal('sales_tax', 10, 2)->default(0)->after('refundable_name');
            $table->string('sales_tax_name')->nullable()->after('sales_tax');
            $table->string('service_charge_name')->nullable()->after('service_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'gratuity',
                'gratuity_name',
                'refundable',
                'refundable_name',
                'sales_tax',
                'sales_tax_name',
                'service_charge_name',
            ]);
        });
    }
};
