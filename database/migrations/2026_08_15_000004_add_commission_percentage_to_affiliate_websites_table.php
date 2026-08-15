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
        if (Schema::hasTable('affiliate_websites') && !Schema::hasColumn('affiliate_websites', 'commission_percentage')) {
            Schema::table('affiliate_websites', function (Blueprint $table) {
                $table->decimal('commission_percentage', 5, 2)->nullable()->after('is_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('affiliate_websites') && Schema::hasColumn('affiliate_websites', 'commission_percentage')) {
            Schema::table('affiliate_websites', function (Blueprint $table) {
                $table->dropColumn('commission_percentage');
            });
        }
    }
};
