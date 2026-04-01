<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entertainers', function (Blueprint $table) {
            $table->decimal('default_commission_percentage', 5, 2)->default(10)->after('font_family');
        });
    }

    public function down(): void
    {
        Schema::table('entertainers', function (Blueprint $table) {
            $table->dropColumn('default_commission_percentage');
        });
    }
};
