<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entertainer_wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entertainer_id')->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();
            $table->enum('type', ['credit', 'debit', 'commission', 'adjustment'])->default('adjustment');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entertainer_wallet_transactions');
    }
};
