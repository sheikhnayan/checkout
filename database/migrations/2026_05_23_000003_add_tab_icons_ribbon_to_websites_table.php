<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'guest_tab_icon')) {
                $table->string('guest_tab_icon', 50)->nullable()->after('guest_tab_color');
            }
            if (!Schema::hasColumn('websites', 'package_tab_icon')) {
                $table->string('package_tab_icon', 50)->nullable()->after('package_tab_color');
            }
            if (!Schema::hasColumn('websites', 'package_tab_ribbon')) {
                $table->string('package_tab_ribbon', 80)->nullable()->after('package_tab_icon');
            }
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn(['guest_tab_icon', 'package_tab_icon', 'package_tab_ribbon']);
        });
    }
};
