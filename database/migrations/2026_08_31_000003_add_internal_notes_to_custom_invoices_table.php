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
        Schema::table('custom_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('custom_invoices', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('custom_invoices', 'internal_notes')) {
                $table->dropColumn('internal_notes');
            }
        });
    }
};
