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
            $table->decimal('processing_fee', 8, 4)->nullable()->default(0)->after('service_charge_fee')->comment('Processing fee as percentage or flat amount');
            $table->enum('processing_fee_type', ['percentage', 'flat'])->default('percentage')->after('processing_fee')->comment('Type of processing fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('websites', function (Blueprint $table) {
            $table->dropColumn(['processing_fee', 'processing_fee_type']);
        });
    }
};
