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
        if (!Schema::hasTable('websites')) {
            return;
        }

        if (!Schema::hasColumn('websites', 'entertainer_submission_emails')) {
            Schema::table('websites', function (Blueprint $table) {
                $table->json('entertainer_submission_emails')->nullable()->after('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('websites')) {
            return;
        }

        if (Schema::hasColumn('websites', 'entertainer_submission_emails')) {
            Schema::table('websites', function (Blueprint $table) {
                $table->dropColumn('entertainer_submission_emails');
            });
        }
    }
};
