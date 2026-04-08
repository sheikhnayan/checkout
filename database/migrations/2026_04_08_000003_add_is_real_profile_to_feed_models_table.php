<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('feed_models', 'is_real_profile')) {
            Schema::table('feed_models', function (Blueprint $table) {
                $table->boolean('is_real_profile')->default(false)->after('bio');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('feed_models', 'is_real_profile')) {
            Schema::table('feed_models', function (Blueprint $table) {
                $table->dropColumn('is_real_profile');
            });
        }
    }
};
