<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_categories', function (Blueprint $table) {
            if (!Schema::hasColumn('package_categories', 'icon')) {
                $table->string('icon', 50)->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('package_categories', function (Blueprint $table) {
            if (Schema::hasColumn('package_categories', 'icon')) {
                $table->dropColumn('icon');
            }
        });
    }
};
