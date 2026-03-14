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
        // Backfill existing websites with null status.
        DB::table('websites')->whereNull('status')->update(['status' => '1']);

        // Ensure newly created websites default to active.
        Schema::table('websites', function (Blueprint $table) {
            $table->string('status')->nullable()->default('1')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->string('status')->nullable()->default(null)->change();
        });
    }
};
