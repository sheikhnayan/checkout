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
            $table->string('ticket_qr_code')->nullable()->unique()->after('transaction_id');
            $table->boolean('checked_in_status')->default(false)->after('status');
            $table->dateTime('checked_in_at_pacific')->nullable()->after('checked_in_status');
            $table->unsignedBigInteger('checked_in_by_user_id')->nullable()->after('checked_in_at_pacific');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'ticket_qr_code',
                'checked_in_status',
                'checked_in_at_pacific',
                'checked_in_by_user_id',
            ]);
        });
    }
};
