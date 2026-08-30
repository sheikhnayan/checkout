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
        Schema::table('custom_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_invoice_items', 'guests')) {
                $table->integer('guests')->default(1)->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('custom_invoice_items', 'guests')) {
                $table->dropColumn('guests');
            }
        });
    }
};
