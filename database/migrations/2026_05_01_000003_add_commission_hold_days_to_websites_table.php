<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->unsignedSmallInteger('commission_hold_days')->nullable()->after('withdraw_charge');
            $table->unsignedSmallInteger('commission_hold_days_authorize')->nullable()->after('commission_hold_days');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn(['commission_hold_days', 'commission_hold_days_authorize']);
        });
    }
};
