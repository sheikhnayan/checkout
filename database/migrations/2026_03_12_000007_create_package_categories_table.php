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
        if (!Schema::hasTable('package_categories')) {
            Schema::create('package_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('website_id')->nullable()->index();
                $table->string('name', 191);
                $table->timestamps();

                $table->unique(['website_id', 'name']);
            });
        }

        if (!Schema::hasColumn('packages', 'package_category_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->unsignedBigInteger('package_category_id')->nullable()->after('website_id')->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('packages', 'package_category_id')) {
            Schema::table('packages', function (Blueprint $table) {
                $table->dropColumn('package_category_id');
            });
        }

        Schema::dropIfExists('package_categories');
    }
};