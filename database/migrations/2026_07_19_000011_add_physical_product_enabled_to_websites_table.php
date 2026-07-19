<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->boolean('physical_product_enabled')
                ->default(false)
                ->after('clublifter_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn('physical_product_enabled');
        });
    }
};
