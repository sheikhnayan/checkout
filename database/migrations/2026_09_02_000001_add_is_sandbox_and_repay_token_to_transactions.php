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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'is_sandbox')) {
                $table->boolean('is_sandbox')->default(false)->after('status')->index();
            }
            if (!Schema::hasColumn('transactions', 'repay_token')) {
                $table->string('repay_token', 64)->nullable()->unique()->after('is_sandbox');
            }
            if (!Schema::hasColumn('transactions', 'repay_paid_at')) {
                $table->timestamp('repay_paid_at')->nullable()->after('repay_token');
            }
            if (!Schema::hasColumn('transactions', 'repay_gateway_trans_id')) {
                $table->string('repay_gateway_trans_id', 128)->nullable()->after('repay_paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('transactions', 'repay_gateway_trans_id')) {
                $columnsToDrop[] = 'repay_gateway_trans_id';
            }
            if (Schema::hasColumn('transactions', 'repay_paid_at')) {
                $columnsToDrop[] = 'repay_paid_at';
            }
            if (Schema::hasColumn('transactions', 'repay_token')) {
                $columnsToDrop[] = 'repay_token';
            }
            if (Schema::hasColumn('transactions', 'is_sandbox')) {
                $columnsToDrop[] = 'is_sandbox';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
