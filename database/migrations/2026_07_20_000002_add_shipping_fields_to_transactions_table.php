<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'shipping_same_as_billing')) {
                $table->boolean('shipping_same_as_billing')->default(false)->after('payment_zip_code');
            }

            if (!Schema::hasColumn('transactions', 'shipping_first_name')) {
                $table->string('shipping_first_name')->nullable()->after('shipping_same_as_billing');
            }

            if (!Schema::hasColumn('transactions', 'shipping_last_name')) {
                $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
            }

            if (!Schema::hasColumn('transactions', 'shipping_phone')) {
                $table->string('shipping_phone')->nullable()->after('shipping_last_name');
            }

            if (!Schema::hasColumn('transactions', 'shipping_email')) {
                $table->string('shipping_email')->nullable()->after('shipping_phone');
            }

            if (!Schema::hasColumn('transactions', 'shipping_address')) {
                $table->string('shipping_address')->nullable()->after('shipping_email');
            }

            if (!Schema::hasColumn('transactions', 'shipping_country')) {
                $table->string('shipping_country')->nullable()->after('shipping_address');
            }

            if (!Schema::hasColumn('transactions', 'shipping_state')) {
                $table->string('shipping_state')->nullable()->after('shipping_country');
            }

            if (!Schema::hasColumn('transactions', 'shipping_city')) {
                $table->string('shipping_city')->nullable()->after('shipping_state');
            }

            if (!Schema::hasColumn('transactions', 'shipping_zip_code')) {
                $table->string('shipping_zip_code')->nullable()->after('shipping_city');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('transactions')) {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            foreach (['shipping_zip_code', 'shipping_city', 'shipping_state', 'shipping_country', 'shipping_address', 'shipping_email', 'shipping_phone', 'shipping_last_name', 'shipping_first_name', 'shipping_same_as_billing'] as $column) {
                if (Schema::hasColumn('transactions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};