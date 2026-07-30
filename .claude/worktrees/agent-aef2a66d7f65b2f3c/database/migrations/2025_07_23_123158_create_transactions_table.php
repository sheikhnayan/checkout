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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('package_id')->nullable();
            $table->string('package_first_name')->nullable();
            $table->string('package_last_name')->nullable();
            $table->string('package_phone')->nullable();
            $table->string('package_email')->nullable();
            $table->string('package_dob')->nullable();
            $table->longText('package_note')->nullable();
            $table->string('transportation_pickup_time')->nullable();
            $table->string('transportation_address')->nullable();
            $table->string('transportation_phone')->nullable();
            $table->string('transportation_guest')->nullable();
            $table->longText('transportation_note')->nullable();
            $table->string('payment_first_name')->nullable();
            $table->string('payment_last_name')->nullable();
            $table->string('payment_phone')->nullable();
            $table->string('payment_email')->nullable();
            $table->string('payment_address')->nullable();
            $table->string('payment_city')->nullable();
            $table->string('payment_state')->nullable();
            $table->string('payment_country')->nullable();
            $table->string('payment_dob')->nullable();
            $table->string('payment_zip_code')->nullable();
            $table->string('type')->nullable();
            $table->string('status')->default(1);
            $table->string('website_id')->nullable();
            $table->string('event_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
