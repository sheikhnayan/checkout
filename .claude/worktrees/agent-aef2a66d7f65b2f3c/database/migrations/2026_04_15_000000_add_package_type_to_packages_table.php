<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->enum('package_type', ['ticket', 'table'])->default('ticket')->after('number_of_guest');
            $table->integer('daily_ticket_limit')->nullable()->after('package_type');
            $table->integer('daily_table_limit')->nullable()->after('daily_ticket_limit');
            $table->integer('guests_per_table')->nullable()->after('daily_table_limit');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['package_type', 'daily_ticket_limit', 'daily_table_limit', 'guests_per_table']);
        });
    }
};
