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
        Schema::table('help_center_items', function (Blueprint $table) {
            if (!Schema::hasColumn('help_center_items', 'file_path')) {
                $table->string('file_path')->nullable()->after('url');
            }
        });

        // Modify enum/type column safely if needed
        try {
            DB::statement("ALTER TABLE help_center_items MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'form'");
        } catch (\Exception $e) {
            // Ignore if already varchar or driver doesn't support raw alter
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('help_center_items', function (Blueprint $table) {
            if (Schema::hasColumn('help_center_items', 'file_path')) {
                $table->dropColumn('file_path');
            }
        });
    }
};
