<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('feed_models') || !Schema::hasTable('entertainers') || !Schema::hasColumn('feed_models', 'is_real_profile')) {
            return;
        }

        DB::table('feed_models')->update(['is_real_profile' => false]);

        DB::table('feed_models')
            ->whereIn('id', function ($query) {
                $query->select('feed_model_id')
                    ->from('entertainers')
                    ->whereNotNull('feed_model_id');
            })
            ->update(['is_real_profile' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('feed_models') || !Schema::hasColumn('feed_models', 'is_real_profile')) {
            return;
        }

        DB::table('feed_models')->update(['is_real_profile' => false]);
    }
};
