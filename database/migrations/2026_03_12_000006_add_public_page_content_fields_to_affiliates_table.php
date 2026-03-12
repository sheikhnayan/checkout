<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('display_name');
            $table->string('hero_subtitle', 500)->nullable()->after('hero_title');
            $table->text('secondary_description')->nullable()->after('description');
            $table->json('gallery_images')->nullable()->after('banner_image');
        });
    }

    public function down(): void
    {
        Schema::table('affiliates', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_subtitle',
                'secondary_description',
                'gallery_images',
            ]);
        });
    }
};
