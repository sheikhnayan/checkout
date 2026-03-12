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
        Schema::disableForeignKeyConstraints();

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('affiliate_id')->nullable()->after('website_id');
            $table->decimal('affiliate_commission_percentage', 5, 2)->nullable()->after('affiliate_id');
            $table->decimal('affiliate_commission_amount', 12, 2)->default(0)->after('affiliate_commission_percentage');
            $table->string('affiliate_source')->nullable()->after('affiliate_commission_amount');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['affiliate_id']);
            $table->dropColumn(['affiliate_id', 'affiliate_commission_percentage', 'affiliate_commission_amount', 'affiliate_source']);
        });
    }
};
