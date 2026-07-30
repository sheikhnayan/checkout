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
        if (Schema::hasTable('package_categories') && !Schema::hasColumn('package_categories', 'is_archieved')) {
            Schema::table('package_categories', function (Blueprint $table) {
                $table->string('is_archieved')->default('0')->nullable()->after('sort_order');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('package_categories') && Schema::hasColumn('package_categories', 'is_archieved')) {
            Schema::table('package_categories', function (Blueprint $table) {
                $table->dropColumn('is_archieved');
            });
        }
    }
};
