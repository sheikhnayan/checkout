<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a global Live/Sandbox toggle for the gateway. Defaults to TRUE
     * (sandbox) so existing behavior is unchanged until an admin flips it to
     * Live. The checkout resolves the environment per-website first, then falls
     * back to this global flag (see TransactionController::authorizeNetEnvironment).
     */
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'sandbox_mode')) {
                $table->boolean('sandbox_mode')->default(true)->after('payment_method');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'sandbox_mode')) {
                $table->dropColumn('sandbox_mode');
            }
        });
    }
};
