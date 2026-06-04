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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('checkin_photo_front_path')->nullable()->after('checkin_photo_path');
            $table->string('checkin_photo_back_path')->nullable()->after('checkin_photo_front_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['checkin_photo_front_path', 'checkin_photo_back_path']);
        });
    }
};
