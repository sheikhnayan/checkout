<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    public const AUDIENCE_CLUB = 'club';
    public const AUDIENCE_AFFILIATE = 'affiliate';
    public const AUDIENCE_ENTERTAINER = 'entertainer';

    public const ALLOWED_AUDIENCES = [
        self::AUDIENCE_CLUB,
        self::AUDIENCE_AFFILIATE,
        self::AUDIENCE_ENTERTAINER,
    ];

    public const DISCOUNT_METHOD_CODE = 'code';
    public const DISCOUNT_METHOD_AUTOMATIC = 'automatic';

    public const DISCOUNT_TYPE_PERCENTAGE = 'percentage';
    public const DISCOUNT_TYPE_FIXED = 'fixed';

    public const APPLIES_TO_ALL_PACKAGES = 'all_packages';
    public const APPLIES_TO_SPECIFIC_PACKAGES = 'specific_packages';

    public const MIN_REQUIREMENT_NONE = 'none';
    public const MIN_REQUIREMENT_AMOUNT = 'amount';
    public const MIN_REQUIREMENT_QUANTITY = 'quantity';

    protected $casts = [
        'applies_to_package_ids' => 'array',
        'discount_value' => 'float',
        'min_purchase_amount' => 'float',
        'min_purchase_quantity' => 'integer',
        'usage_limit_total' => 'integer',
        'usage_count' => 'integer',
        'limit_one_per_customer' => 'boolean',
        'combine_product_discounts' => 'boolean',
        'combine_order_discounts' => 'boolean',
        'combine_shipping_discounts' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function getTargetNameAttribute(): ?string
    {
        if ($this->audience === self::AUDIENCE_AFFILIATE && $this->affiliate) {
            return $this->affiliate->display_name ?: optional($this->affiliate->user)->name;
        }

        if ($this->audience === self::AUDIENCE_ENTERTAINER && $this->entertainer) {
            return $this->entertainer->display_name ?: optional($this->entertainer->user)->name;
        }

        return null;
    }
}
