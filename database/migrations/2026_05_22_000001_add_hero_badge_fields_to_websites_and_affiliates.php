<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            if (!Schema::hasColumn('websites', 'hero_badge_1_label')) {
                $table->string('hero_badge_1_label', 80)->nullable()->after('hero_subtitle');
            }
            if (!Schema::hasColumn('websites', 'hero_badge_1_sub')) {
                $table->string('hero_badge_1_sub', 120)->nullable()->after('hero_badge_1_label');
            }
            if (!Schema::hasColumn('websites', 'hero_badge_2_label')) {
                $table->string('hero_badge_2_label', 80)->nullable()->after('hero_badge_1_sub');
            }
            if (!Schema::hasColumn('websites', 'hero_badge_2_sub')) {
                $table->string('hero_badge_2_sub', 120)->nullable()->after('hero_badge_2_label');
            }
        });

        Schema::table('promoters', function (Blueprint $table) {
            if (!Schema::hasColumn('promoters', 'hero_badge_1_label')) {
                $table->string('hero_badge_1_label', 80)->nullable()->after('hero_subtitle');
            }
            if (!Schema::hasColumn('promoters', 'hero_badge_1_sub')) {
                $table->string('hero_badge_1_sub', 120)->nullable()->after('hero_badge_1_label');
            }
            if (!Schema::hasColumn('promoters', 'hero_badge_2_label')) {
                $table->string('hero_badge_2_label', 80)->nullable()->after('hero_badge_1_sub');
            }
            if (!Schema::hasColumn('promoters', 'hero_badge_2_sub')) {
                $table->string('hero_badge_2_sub', 120)->nullable()->after('hero_badge_2_label');
            }
        });
    }

    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $drop = [];
            foreach (['hero_badge_1_label', 'hero_badge_1_sub', 'hero_badge_2_label', 'hero_badge_2_sub'] as $column) {
                if (Schema::hasColumn('websites', $column)) {
                    $drop[] = $column;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });

        Schema::table('promoters', function (Blueprint $table) {
            $drop = [];
            foreach (['hero_badge_1_label', 'hero_badge_1_sub', 'hero_badge_2_label', 'hero_badge_2_sub'] as $column) {
                if (Schema::hasColumn('promoters', $column)) {
                    $drop[] = $column;
                }
            }
            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};