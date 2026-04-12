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
        Schema::table('feed_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('feed_posts', 'roll_call_start_date')) {
                $table->date('roll_call_start_date')->nullable()->after('roll_call_date');
            }

            if (!Schema::hasColumn('feed_posts', 'roll_call_end_date')) {
                $table->date('roll_call_end_date')->nullable()->after('roll_call_start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feed_posts', function (Blueprint $table) {
            if (Schema::hasColumn('feed_posts', 'roll_call_end_date')) {
                $table->dropColumn('roll_call_end_date');
            }

            if (Schema::hasColumn('feed_posts', 'roll_call_start_date')) {
                $table->dropColumn('roll_call_start_date');
            }
        });
    }
};
