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
        Schema::table('w9_forms', function (Blueprint $table) {
            $table->longText('pdf_form_data')->nullable()->after('city_state_zip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('w9_forms', function (Blueprint $table) {
            $table->dropColumn('pdf_form_data');
        });
    }
};
