<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('packages') && !Schema::hasColumn('packages', 'physical_product_enabled')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->boolean('physical_product_enabled')->default(false)->after('transportation');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('packages') && Schema::hasColumn('packages', 'physical_product_enabled')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('physical_product_enabled');
            });
        }
    }
};