<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('feed_posts')) {
            return;
        }

        Schema::table('feed_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('feed_posts', 'review_required')) {
                $table->boolean('review_required')->default(false)->after('approved_by');
            }

            if (!Schema::hasColumn('feed_posts', 'reviewed_at')) {
                $table->timestamp('reviewed_at')->nullable()->after('review_required');
            }

            $table->index(['website_id', 'review_required'], 'feed_posts_website_id_review_required_index');
        });

        DB::table('feed_posts')
            ->where('approval_status', 'pending')
            ->update([
                'review_required' => true,
                'reviewed_at' => null,
            ]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('feed_posts')) {
            return;
        }

        Schema::table('feed_posts', function (Blueprint $table) {
            try {
                $table->dropIndex('feed_posts_website_id_review_required_index');
            } catch (\Throwable $e) {
                // Index may not exist on some deployments.
            }

            $columnsToDrop = [];

            if (Schema::hasColumn('feed_posts', 'review_required')) {
                $columnsToDrop[] = 'review_required';
            }

            if (Schema::hasColumn('feed_posts', 'reviewed_at')) {
                $columnsToDrop[] = 'reviewed_at';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
