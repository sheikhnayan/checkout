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
        Schema::create('shared_carts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // Short random code like abc123def
            $table->longText('cart_data'); // Full JSON cart data
            $table->string('website_slug'); // Website slug for the cart
            $table->timestamps();
            $table->index('code');
            $table->index('website_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_carts');
    }
};
