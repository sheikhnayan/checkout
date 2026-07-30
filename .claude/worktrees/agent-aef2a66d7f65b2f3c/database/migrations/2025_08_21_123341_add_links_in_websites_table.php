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
        Schema::table('websites', function (Blueprint $table) {
            $table->string('back_link')->nullable();
            $table->string('back_text')->nullable();
            $table->string('footer_text')->nullable();
            $table->string('authorize_app_key')->nullable();
            $table->string('authorize_secret_key')->nullable();
            $table->string('stripe_app_key')->nullable();
            $table->string('stripe_secret_key')->nullable();
            // $table->string('authorize_app_id')->nullable();
            // $table->string('back_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            //
        });
    }
};
