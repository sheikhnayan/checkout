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
            if (!Schema::hasColumn('websites', 'package_section_title')) {
                $table->string('package_section_title', 120)->nullable()->after('package_tab_ribbon');
            }

            if (!Schema::hasColumn('websites', 'package_section_subtext')) {
                $table->string('package_section_subtext', 255)->nullable()->after('package_section_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('websites', 'package_section_subtext')) {
                $dropColumns[] = 'package_section_subtext';
            }

            if (Schema::hasColumn('websites', 'package_section_title')) {
                $dropColumns[] = 'package_section_title';
            }

            if (!empty($dropColumns)) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
