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
            if (!Schema::hasColumn('transactions', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('transactions', 'admin_notes_by')) {
                $table->string('admin_notes_by')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('transactions', 'admin_notes_at')) {
                $table->timestamp('admin_notes_at')->nullable()->after('admin_notes_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'admin_notes')) {
                $table->dropColumn('admin_notes');
            }
            if (Schema::hasColumn('transactions', 'admin_notes_by')) {
                $table->dropColumn('admin_notes_by');
            }
            if (Schema::hasColumn('transactions', 'admin_notes_at')) {
                $table->dropColumn('admin_notes_at');
            }
        });
    }
};
