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
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->string('discount_method')->default('code')->after('audience');
            $table->string('discount_value_type')->nullable()->after('discount_method');
            $table->decimal('discount_value', 12, 2)->nullable()->after('discount_value_type');

            $table->string('applies_to')->default('all_packages')->after('discount_value');
            $table->json('applies_to_package_ids')->nullable()->after('applies_to');

            $table->string('eligibility')->default('all_customers')->after('applies_to_package_ids');

            $table->string('min_requirement_type')->default('none')->after('eligibility');
            $table->decimal('min_purchase_amount', 12, 2)->nullable()->after('min_requirement_type');
            $table->unsignedInteger('min_purchase_quantity')->nullable()->after('min_purchase_amount');

            $table->unsignedInteger('usage_limit_total')->nullable()->after('min_purchase_quantity');
            $table->unsignedInteger('usage_count')->default(0)->after('usage_limit_total');
            $table->boolean('limit_one_per_customer')->default(false)->after('usage_count');

            $table->boolean('combine_product_discounts')->default(false)->after('limit_one_per_customer');
            $table->boolean('combine_order_discounts')->default(false)->after('combine_product_discounts');
            $table->boolean('combine_shipping_discounts')->default(false)->after('combine_order_discounts');

            $table->timestamp('starts_at')->nullable()->after('combine_shipping_discounts');
            $table->timestamp('ends_at')->nullable()->after('starts_at');
            $table->boolean('is_active')->default(true)->after('ends_at');
        });

        Schema::table('promo_codes', function (Blueprint $table) {
            $table->index(['website_id', 'promo_code']);
            $table->index(['website_id', 'is_archieved', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promo_codes', function (Blueprint $table) {
            $table->dropIndex(['website_id', 'promo_code']);
            $table->dropIndex(['website_id', 'is_archieved', 'is_active']);

            $table->dropColumn([
                'discount_method',
                'discount_value_type',
                'discount_value',
                'applies_to',
                'applies_to_package_ids',
                'eligibility',
                'min_requirement_type',
                'min_purchase_amount',
                'min_purchase_quantity',
                'usage_limit_total',
                'usage_count',
                'limit_one_per_customer',
                'combine_product_discounts',
                'combine_order_discounts',
                'combine_shipping_discounts',
                'starts_at',
                'ends_at',
                'is_active',
            ]);
        });
    }
};
