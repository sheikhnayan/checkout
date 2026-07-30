<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('audience')->default('club')->after('website_id');
            $table->unsignedBigInteger('affiliate_id')->nullable()->after('audience');
            $table->unsignedBigInteger('entertainer_id')->nullable()->after('affiliate_id');
            $table->index('audience');
            $table->index('affiliate_id');
            $table->index('entertainer_id');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropIndex(['audience']);
            $table->dropIndex(['affiliate_id']);
            $table->dropIndex(['entertainer_id']);
            $table->dropColumn(['audience', 'affiliate_id', 'entertainer_id']);
        });
    }
};