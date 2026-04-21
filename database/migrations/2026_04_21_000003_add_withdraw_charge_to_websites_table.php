<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->decimal('withdraw_charge', 5, 2)->default(0)->after('processing_fee_type')
                ->comment('Withdrawal fee percentage charged to entertainers on this website');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('withdraw_charge');
        });
    }
};
