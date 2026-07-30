<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('affiliate_commission_status')->nullable()->after('affiliate_commission_amount');
            $table->timestamp('affiliate_commission_hold_until')->nullable()->after('affiliate_commission_status');
            $table->timestamp('affiliate_commission_approved_at')->nullable()->after('affiliate_commission_hold_until');
            $table->timestamp('affiliate_commission_reversed_at')->nullable()->after('affiliate_commission_approved_at');

            $table->string('entertainer_commission_status')->nullable()->after('entertainer_commission_amount');
            $table->timestamp('entertainer_commission_hold_until')->nullable()->after('entertainer_commission_status');
            $table->timestamp('entertainer_commission_approved_at')->nullable()->after('entertainer_commission_hold_until');
            $table->timestamp('entertainer_commission_reversed_at')->nullable()->after('entertainer_commission_approved_at');

            $table->index('affiliate_commission_status');
            $table->index('affiliate_commission_hold_until');
            $table->index('entertainer_commission_status');
            $table->index('entertainer_commission_hold_until');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['affiliate_commission_status']);
            $table->dropIndex(['affiliate_commission_hold_until']);
            $table->dropIndex(['entertainer_commission_status']);
            $table->dropIndex(['entertainer_commission_hold_until']);

            $table->dropColumn([
                'affiliate_commission_status',
                'affiliate_commission_hold_until',
                'affiliate_commission_approved_at',
                'affiliate_commission_reversed_at',
                'entertainer_commission_status',
                'entertainer_commission_hold_until',
                'entertainer_commission_approved_at',
                'entertainer_commission_reversed_at',
            ]);
        });
    }
};
