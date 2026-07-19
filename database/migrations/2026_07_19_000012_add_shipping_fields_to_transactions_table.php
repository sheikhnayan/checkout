<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->boolean('shipping_same_as_billing')->default(false)->after('payment_zip_code');
            $table->string('shipping_first_name')->nullable()->after('shipping_same_as_billing');
            $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
            $table->string('shipping_phone')->nullable()->after('shipping_last_name');
            $table->string('shipping_email')->nullable()->after('shipping_phone');
            $table->string('shipping_address')->nullable()->after('shipping_email');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_country')->nullable()->after('shipping_state');
            $table->string('shipping_zip_code')->nullable()->after('shipping_country');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_same_as_billing',
                'shipping_first_name',
                'shipping_last_name',
                'shipping_phone',
                'shipping_email',
                'shipping_address',
                'shipping_city',
                'shipping_state',
                'shipping_country',
                'shipping_zip_code',
            ]);
        });
    }
};
