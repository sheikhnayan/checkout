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
            $table->string('hero_title')->nullable()->after('description_label');
            $table->string('hero_subtitle', 500)->nullable()->after('hero_title');
            $table->text('secondary_description')->nullable()->after('text_description');
            $table->json('gallery_images')->nullable()->after('logo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_subtitle',
                'secondary_description',
                'gallery_images',
            ]);
        });
    }
};
