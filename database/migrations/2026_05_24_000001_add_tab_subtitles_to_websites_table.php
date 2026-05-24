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
            if (!Schema::hasColumn('websites', 'guest_tab_subtitle')) {
                $table->string('guest_tab_subtitle', 120)->nullable()->after('guest_list_button_text');
            }
            if (!Schema::hasColumn('websites', 'package_tab_subtitle')) {
                $table->string('package_tab_subtitle', 120)->nullable()->after('package_button_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            if (Schema::hasColumn('websites', 'guest_tab_subtitle')) {
                $table->dropColumn('guest_tab_subtitle');
            }
            if (Schema::hasColumn('websites', 'package_tab_subtitle')) {
                $table->dropColumn('package_tab_subtitle');
            }
        });
    }
};
