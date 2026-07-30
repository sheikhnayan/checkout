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
        Schema::table('websites', function (Blueprint $table) {
            $table->string('visa_logo')->nullable()->after('transportation_confirmation_text');
            $table->string('mastercard_logo')->nullable()->after('visa_logo');
            $table->string('amex_logo')->nullable()->after('mastercard_logo');
            $table->string('google_pay_logo')->nullable()->after('amex_logo');
            $table->string('apple_pay_logo')->nullable()->after('google_pay_logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn([
                'visa_logo',
                'mastercard_logo', 
                'amex_logo',
                'google_pay_logo',
                'apple_pay_logo'
            ]);
        });
    }
};
