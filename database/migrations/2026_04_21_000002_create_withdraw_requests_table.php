<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdraw_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type');                          // 'promoter' or 'entertainer'
            $table->unsignedBigInteger('payout_method_id')->nullable(); // null if method was deleted
            $table->unsignedBigInteger('website_id')->nullable();  // entertainer's website for charge lookup
            $table->decimal('amount', 10, 2);                      // requested amount
            $table->decimal('fee_percentage', 5, 2)->default(0);   // charge % at time of request
            $table->decimal('fee_amount', 10, 2)->default(0);      // computed fee
            $table->decimal('net_amount', 10, 2);                  // amount - fee
            $table->string('status')->default('pending');           // pending | done | rejected
            $table->text('notes')->nullable();                      // requester notes
            $table->text('admin_notes')->nullable();               // admin notes on status change
            $table->json('method_snapshot')->nullable();            // snapshot of payout method details
            $table->timestamps();

            $table->index(['owner_id', 'owner_type']);
            $table->index('status');
            $table->index('website_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdraw_requests');
    }
};
