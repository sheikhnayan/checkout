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
        Schema::table('general_addons', function (Blueprint $table) {
            $table->string('status')->nullable()->default('1')->after('price');
        });

        DB::table('general_addons')->whereNull('status')->update(['status' => '1']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('general_addons', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
