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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('authorize_key')->nullable();
            $table->string('authorize_secret')->nullable();
            $table->string('stripe_key')->nullable();
            $table->string('stripe_secret')->nullable();
            $table->string('payment_method')->default('authorize'); // Default to 'authorize'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
