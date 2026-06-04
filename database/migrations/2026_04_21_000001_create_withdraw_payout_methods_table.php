<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraw_payout_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type'); // 'affiliate' or 'entertainer'
            $table->string('label');       // e.g. "My Chase Checking"
            $table->string('type');        // bank_transfer | check | wire | paypal | zelle | other
            $table->json('details');       // flexible JSON: account number, routing, bank name, etc.
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['owner_id', 'owner_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraw_payout_methods');
    }
};
