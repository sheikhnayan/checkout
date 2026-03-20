<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shared_carts', function (Blueprint $table) {
            $table->string('affiliate_slug')->nullable()->after('website_slug');
            $table->string('club_slug')->nullable()->after('affiliate_slug');
        });
    }

    public function down(): void
    {
        Schema::table('shared_carts', function (Blueprint $table) {
            $table->dropColumn(['affiliate_slug', 'club_slug']);
        });
    }
};
