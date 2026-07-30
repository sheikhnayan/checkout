<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->decimal('affiliate_withdraw_charge', 5, 2)->default(0)
                ->after('payment_method')
                ->comment('Global withdrawal fee percentage charged to affiliates (set by super admin)');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('affiliate_withdraw_charge');
        });
    }
};
