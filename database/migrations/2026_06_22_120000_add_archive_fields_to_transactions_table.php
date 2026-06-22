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
            if (!Schema::hasColumn('transactions', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->index();
            }

            if (!Schema::hasColumn('transactions', 'archived_by_user_id')) {
                $table->foreignId('archived_by_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'archived_by_user_id')) {
                $table->dropConstrainedForeignId('archived_by_user_id');
            }

            if (Schema::hasColumn('transactions', 'archived_at')) {
                $table->dropColumn('archived_at');
            }
        });
    }
};
