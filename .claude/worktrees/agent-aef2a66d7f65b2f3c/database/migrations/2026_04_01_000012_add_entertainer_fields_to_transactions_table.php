<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('entertainer_id')->nullable()->after('affiliate_source');
            $table->decimal('entertainer_commission_percentage', 5, 2)->nullable()->after('entertainer_id');
            $table->decimal('entertainer_commission_amount', 12, 2)->default(0)->after('entertainer_commission_percentage');
            $table->string('entertainer_source')->nullable()->after('entertainer_commission_amount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'entertainer_id',
                'entertainer_commission_percentage',
                'entertainer_commission_amount',
                'entertainer_source',
            ]);
        });
    }
};
