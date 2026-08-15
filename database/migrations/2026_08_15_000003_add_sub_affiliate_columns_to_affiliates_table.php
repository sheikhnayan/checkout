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
        Schema::table('affiliates', function (Blueprint $table) {
            if (!Schema::hasColumn('affiliates', 'parent_affiliate_id')) {
                $table->unsignedBigInteger('parent_affiliate_id')->nullable()->after('user_id')->index();
            }
            if (!Schema::hasColumn('affiliates', 'is_sub_affiliate')) {
                $table->boolean('is_sub_affiliate')->default(false)->after('parent_affiliate_id');
            }
            if (!Schema::hasColumn('affiliates', 'sub_affiliate_permissions')) {
                $table->json('sub_affiliate_permissions')->nullable()->after('is_sub_affiliate');
            }
            if (!Schema::hasColumn('affiliates', 'require_onboarding_form')) {
                $table->boolean('require_onboarding_form')->default(false)->after('sub_affiliate_permissions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            if (Schema::hasColumn('affiliates', 'require_onboarding_form')) {
                $table->dropColumn('require_onboarding_form');
            }
            if (Schema::hasColumn('affiliates', 'sub_affiliate_permissions')) {
                $table->dropColumn('sub_affiliate_permissions');
            }
            if (Schema::hasColumn('affiliates', 'is_sub_affiliate')) {
                $table->dropColumn('is_sub_affiliate');
            }
            if (Schema::hasColumn('affiliates', 'parent_affiliate_id')) {
                $table->dropColumn('parent_affiliate_id');
            }
        });
    }
};
