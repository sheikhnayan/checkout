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
        Schema::table('events', function (Blueprint $table) {
            $table->string('hero_title', 255)->nullable()->change();
            $table->string('hero_subtitle', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            // AppServiceProvider sets default string length to 191.
            $table->string('hero_title', 191)->nullable()->change();
            $table->string('hero_subtitle', 191)->nullable()->change();
        });
    }
};
