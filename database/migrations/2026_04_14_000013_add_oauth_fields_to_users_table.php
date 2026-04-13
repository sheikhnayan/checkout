<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'oauth_provider')) {
                $table->string('oauth_provider', 50)->nullable()->after('remember_token');
            }

            if (!Schema::hasColumn('users', 'oauth_provider_id')) {
                $table->string('oauth_provider_id', 191)->nullable()->after('oauth_provider');
            }

            if (!Schema::hasColumn('users', 'avatar_url')) {
                $table->string('avatar_url', 1000)->nullable()->after('oauth_provider_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $drop = [];

            if (Schema::hasColumn('users', 'avatar_url')) {
                $drop[] = 'avatar_url';
            }

            if (Schema::hasColumn('users', 'oauth_provider_id')) {
                $drop[] = 'oauth_provider_id';
            }

            if (Schema::hasColumn('users', 'oauth_provider')) {
                $drop[] = 'oauth_provider';
            }

            if (!empty($drop)) {
                $table->dropColumn($drop);
            }
        });
    }
};
