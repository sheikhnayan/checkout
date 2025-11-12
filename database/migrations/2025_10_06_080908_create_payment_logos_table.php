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
        Schema::create('payment_logos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('website_id');
            $table->string('name'); // Payment method name (e.g., "Visa", "Custom Bank", etc.)
            $table->string('logo'); // Logo filename
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->index(['website_id', 'is_active', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logos');
    }
};
