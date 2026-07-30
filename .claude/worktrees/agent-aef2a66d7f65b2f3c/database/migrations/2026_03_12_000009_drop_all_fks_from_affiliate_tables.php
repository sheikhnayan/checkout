<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $fks = [
        'affiliates'                    => ['affiliates_user_id_foreign', 'affiliates_approved_by_foreign'],
        'affiliate_packages'            => ['affiliate_packages_affiliate_id_foreign', 'affiliate_packages_website_id_foreign', 'affiliate_packages_package_id_foreign'],
        'affiliate_wallet_transactions' => ['affiliate_wallet_transactions_affiliate_id_foreign', 'affiliate_wallet_transactions_transaction_id_foreign'],
        'transactions'                  => ['transactions_affiliate_id_foreign'],
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        $db = DB::connection()->getDatabaseName();

        foreach ($this->fks as $table => $constraintNames) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($constraintNames as $constraint) {
                $exists = DB::selectOne(
                    "SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS
                     WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'FOREIGN KEY'",
                    [$db, $table, $constraint]
                );
                if ($exists) {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
                }
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Intentionally left blank — re-adding FKs is not safe on MyISAM servers
    }
};
