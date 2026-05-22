<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'guest_tab_color')) {
                $table->string('guest_tab_color', 20)->nullable()->after('guest_list_button_text');
            }
            if (!Schema::hasColumn('websites', 'package_tab_color')) {
                $table->string('package_tab_color', 20)->nullable()->after('package_button_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn(['guest_tab_color', 'package_tab_color']);
        });
    }
};
