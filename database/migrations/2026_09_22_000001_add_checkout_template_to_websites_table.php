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
        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'checkout_template')) {
                $table->string('checkout_template', 50)->default('default')->nullable()->after('domain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            if (Schema::hasColumn('websites', 'checkout_template')) {
                $table->dropColumn('checkout_template');
            }
        });
    }
};
