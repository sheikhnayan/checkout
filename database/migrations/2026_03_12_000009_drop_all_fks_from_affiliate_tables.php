<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $fks = [
        'affiliates'                   => ['user_id', 'approved_by'],
        'affiliate_packages'           => ['affiliate_id', 'website_id', 'package_id'],
        'affiliate_wallet_transactions'=> ['affiliate_id', 'transaction_id'],
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ($this->fks as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            Schema::table($table, function (Blueprint $table) use ($columns) {
                foreach ($columns as $column) {
                    try {
                        $table->dropForeign([$column]);
                    } catch (\Throwable $e) {
                        // FK didn't exist — skip silently
                    }
                }
            });
        }

        // Drop the affiliate_id FK added to the transactions table
        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'affiliate_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                try {
                    $table->dropForeign(['affiliate_id']);
                } catch (\Throwable $e) {
                    // FK didn't exist — skip silently
                }
            });
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Intentionally left blank — re-adding FKs is not safe on MyISAM servers
    }
};
