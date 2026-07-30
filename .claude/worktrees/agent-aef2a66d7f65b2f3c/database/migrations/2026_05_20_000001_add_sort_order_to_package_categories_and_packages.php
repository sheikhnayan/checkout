<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('package_categories') && !Schema::hasColumn('package_categories', 'sort_order')) {
            Schema::table('package_categories', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('name')->index();
            });
        }

        if (Schema::hasTable('packages') && !Schema::hasColumn('packages', 'sort_order')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->unsignedInteger('sort_order')->default(0)->after('name')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('package_categories') && Schema::hasColumn('package_categories', 'sort_order')) {
            Schema::table('package_categories', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }

        if (Schema::hasTable('packages') && Schema::hasColumn('packages', 'sort_order')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};