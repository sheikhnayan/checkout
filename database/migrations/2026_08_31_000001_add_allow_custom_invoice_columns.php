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
        if (Schema::hasTable('affiliate_websites') && !Schema::hasColumn('affiliate_websites', 'allow_custom_invoice')) {
            Schema::table('affiliate_websites', function (Blueprint $table) {
                $table->boolean('allow_custom_invoice')->default(false)->after('is_active');
            });
        }

        if (Schema::hasTable('entertainers') && !Schema::hasColumn('entertainers', 'allow_custom_invoice')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->boolean('allow_custom_invoice')->default(false)->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('affiliate_websites') && Schema::hasColumn('affiliate_websites', 'allow_custom_invoice')) {
            Schema::table('affiliate_websites', function (Blueprint $table) {
                $table->dropColumn('allow_custom_invoice');
            });
        }

        if (Schema::hasTable('entertainers') && Schema::hasColumn('entertainers', 'allow_custom_invoice')) {
            Schema::table('entertainers', function (Blueprint $table) {
                $table->dropColumn('allow_custom_invoice');
            });
        }
    }
};
