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
            $table->boolean('show_sms_consent')->default(true)->after('transportation_confirmation_text');
            $table->text('sms_consent_text')->nullable()->after('show_sms_consent');

            $table->boolean('show_terms_consent')->default(true)->after('sms_consent_text');
            $table->text('terms_consent_text')->nullable()->after('show_terms_consent');

            $table->boolean('show_transportation_consent')->default(true)->after('terms_consent_text');

            $table->boolean('show_business_expense_consent')->default(false)->after('show_transportation_consent');
            $table->text('business_expense_text')->nullable()->after('show_business_expense_consent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn([
                'show_sms_consent',
                'sms_consent_text',
                'show_terms_consent',
                'terms_consent_text',
                'show_transportation_consent',
                'show_business_expense_consent',
                'business_expense_text',
            ]);
        });
    }
};
