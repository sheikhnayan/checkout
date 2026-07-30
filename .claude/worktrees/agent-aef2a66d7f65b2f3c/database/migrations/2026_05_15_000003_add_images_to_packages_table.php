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
        if (!Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'image')) {
                $table->string('image')->nullable()->after('description');
            }

            if (!Schema::hasColumn('packages', 'mobile_image')) {
                $table->string('mobile_image')->nullable()->after('image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('packages')) {
            return;
        }

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'mobile_image')) {
                $table->dropColumn('mobile_image');
            }

            if (Schema::hasColumn('packages', 'image')) {
                $table->dropColumn('image');
            }
        });
    }
};
