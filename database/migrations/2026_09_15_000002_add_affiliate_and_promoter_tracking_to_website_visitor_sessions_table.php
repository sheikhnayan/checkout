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
        if (Schema::hasTable('website_visitor_sessions')) {
            Schema::table('website_visitor_sessions', function (Blueprint $table) {
                if (!Schema::hasColumn('website_visitor_sessions', 'affiliate_id')) {
                    $table->unsignedBigInteger('affiliate_id')->nullable()->after('website_id');
                }
                if (!Schema::hasColumn('website_visitor_sessions', 'entertainer_id')) {
                    $table->unsignedBigInteger('entertainer_id')->nullable()->after('affiliate_id');
                }
                if (!Schema::hasColumn('website_visitor_sessions', 'channel_source')) {
                    $table->string('channel_source', 32)->default('direct')->after('entertainer_id');
                }

                $table->index(['website_id', 'affiliate_id', 'first_seen_at'], 'wvs_web_aff_seen_idx');
                $table->index(['affiliate_id', 'first_seen_at'], 'wvs_aff_seen_idx');
                $table->index(['channel_source', 'first_seen_at'], 'wvs_channel_seen_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('website_visitor_sessions')) {
            Schema::table('website_visitor_sessions', function (Blueprint $table) {
                if (Schema::hasColumn('website_visitor_sessions', 'affiliate_id')) {
                    $table->dropIndex('wvs_web_aff_seen_idx');
                    $table->dropIndex('wvs_aff_seen_idx');
                    $table->dropColumn('affiliate_id');
                }
                if (Schema::hasColumn('website_visitor_sessions', 'channel_source')) {
                    $table->dropIndex('wvs_channel_seen_idx');
                    $table->dropColumn('channel_source');
                }
                if (Schema::hasColumn('website_visitor_sessions', 'entertainer_id')) {
                    $table->dropColumn('entertainer_id');
                }
            });
        }
    }
};
