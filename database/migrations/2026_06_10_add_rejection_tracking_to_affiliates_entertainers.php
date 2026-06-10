<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add rejection tracking to affiliates table
        if (Schema::hasTable('affiliates')) {
            Schema::table('affiliates', function (Blueprint $table) {
                if (!Schema::hasColumn('affiliates', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('affiliates', 'rejected_by')) {
                    $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                }
            });
        }

        // Add rejection tracking to entertainers table
        if (Schema::hasTable('entertainers')) {
            Schema::table('entertainers', function (Blueprint $table) {
                if (!Schema::hasColumn('entertainers', 'rejected_at')) {
                    $table->timestamp('rejected_at')->nullable()->after('approved_by');
                }
                if (!Schema::hasColumn('entertainers', 'rejected_by')) {
                    $table->unsignedBigInteger('rejected_by')->nullable()->after('rejected_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('affiliates')) {
            Schema::table('affiliates', function (Blueprint $table) {
                if (Schema::hasColumn('affiliates', 'rejected_at')) {
                    $table->dropColumn('rejected_at');
                }
                if (Schema::hasColumn('affiliates', 'rejected_by')) {
                    $table->dropColumn('rejected_by');
                }
            });
        }

        if (Schema::hasTable('entertainers')) {
            Schema::table('entertainers', function (Blueprint $table) {
                if (Schema::hasColumn('entertainers', 'rejected_at')) {
                    $table->dropColumn('rejected_at');
                }
                if (Schema::hasColumn('entertainers', 'rejected_by')) {
                    $table->dropColumn('rejected_by');
                }
            });
        }
    }
};
