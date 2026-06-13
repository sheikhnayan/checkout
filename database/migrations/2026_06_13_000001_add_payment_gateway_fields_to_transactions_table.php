<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds nullable columns to persist the payment-gateway outcome so that
     * held-for-review (Authorize.Net responseCode 4) and fraud/AVS/CVV results
     * can be recorded and reviewed. All columns are nullable so existing rows
     * and existing queries are unaffected.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // 'approved' (responseCode 1) | 'under_review' (responseCode 4)
            $table->string('payment_status')->nullable()->after('status');
            // Raw gateway response code: '1','2','3','4' (Authorize.Net) or 'stripe_succeeded'
            $table->string('gateway_response_code')->nullable()->after('payment_status');
            // Address Verification Service result letter (Y, A, N, U, ...)
            $table->string('gateway_avs_result')->nullable()->after('gateway_response_code');
            // Card Code Verification result letter (M, N, P, S, U)
            $table->string('gateway_cvv_result')->nullable()->after('gateway_avs_result');
            // Human-readable gateway message / reason text
            $table->text('gateway_message')->nullable()->after('gateway_cvv_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'gateway_response_code',
                'gateway_avs_result',
                'gateway_cvv_result',
                'gateway_message',
            ]);
        });
    }
};
