<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Prefer linked general addon status; fallback to active.
        DB::statement("\n            UPDATE addons a\n            LEFT JOIN general_addons g ON g.id = a.addon_id\n            SET a.status = COALESCE(g.status, '1')\n            WHERE a.status IS NULL\n        ");

        Schema::table('addons', function (Blueprint $table) {
            $table->string('status')->nullable()->default('1')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addons', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }
};
