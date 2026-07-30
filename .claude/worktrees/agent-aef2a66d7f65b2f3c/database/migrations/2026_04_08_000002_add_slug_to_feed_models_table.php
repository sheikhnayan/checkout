<?php

use App\Models\FeedModel;
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
        Schema::table('feed_models', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('website_id');
            $table->index('slug');
        });

        FeedModel::query()->select(['id', 'name', 'slug'])->chunkById(100, function ($models): void {
            foreach ($models as $model) {
                if (!blank($model->slug)) {
                    continue;
                }

                $model->slug = FeedModel::generateUniqueSlug((string) $model->name);
                $model->save();
            }
        });

        Schema::table('feed_models', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_models', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};
