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
        Schema::table('nr_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('nr_locations', 'legal_name')) {
                $table->string('legal_name', 200)->nullable()->after('name');
            }
            if (!Schema::hasColumn('nr_locations', 'club_inbox_email')) {
                $table->string('club_inbox_email', 150)->nullable()->after('gm_email');
            }
            if (!Schema::hasColumn('nr_locations', 'nightly_goals')) {
                $table->json('nightly_goals')->nullable()->after('nightly_goal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nr_locations', function (Blueprint $table) {
            if (Schema::hasColumn('nr_locations', 'nightly_goals')) {
                $table->dropColumn('nightly_goals');
            }
            if (Schema::hasColumn('nr_locations', 'club_inbox_email')) {
                $table->dropColumn('club_inbox_email');
            }
            if (Schema::hasColumn('nr_locations', 'legal_name')) {
                $table->dropColumn('legal_name');
            }
        });
    }
};
