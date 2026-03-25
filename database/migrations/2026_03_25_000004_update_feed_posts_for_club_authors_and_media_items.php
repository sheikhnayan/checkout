<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->string('author_mode')->default('model')->after('feed_model_id');
            $table->json('media_items')->nullable()->after('images');
        });

        DB::statement('ALTER TABLE feed_posts MODIFY feed_model_id BIGINT UNSIGNED NULL');

        $posts = DB::table('feed_posts')->select('id', 'images')->get();
        foreach ($posts as $post) {
            $images = json_decode($post->images ?? '[]', true);
            $images = is_array($images) ? array_values(array_filter($images)) : [];
            $mediaItems = array_map(function ($path) {
                return [
                    'type' => 'image',
                    'source' => 'upload',
                    'url' => $path,
                ];
            }, $images);

            DB::table('feed_posts')->where('id', $post->id)->update([
                'media_items' => !empty($mediaItems) ? json_encode($mediaItems) : null,
                'author_mode' => 'model',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            $table->dropColumn(['author_mode', 'media_items']);
        });

        DB::statement('ALTER TABLE feed_posts MODIFY feed_model_id BIGINT UNSIGNED NOT NULL');
    }
};