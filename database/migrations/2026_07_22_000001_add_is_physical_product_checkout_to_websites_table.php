<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('websites') || Schema::hasColumn('websites', 'is_physical_product_checkout')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            $table->boolean('is_physical_product_checkout')->default(false)->after('reservation');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('websites') || !Schema::hasColumn('websites', 'is_physical_product_checkout')) {
            return;
        }

        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('is_physical_product_checkout');
        });
    }
};
