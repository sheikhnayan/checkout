<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'slug',
        'display_name',
        'hero_title',
        'hero_subtitle',
        'hero_badge_1_label',
        'hero_badge_1_sub',
        'hero_badge_2_label',
        'hero_badge_2_sub',
        'description',
        'secondary_description',
        'show_location_section',
        'featured_icon',
        'profile_image',
        'banner_image',
        'gallery_images',
        'facebook_url',
        'instagram_url',
        'youtube_url',
        'tiktok_url',
        'website_url',
        'theme_color',
        'accent_color',
        'background_color',
        'text_color',
        'font_family',
        'default_commission_percentage',
        'commission_hold_days',
        'wallet_balance',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'is_active',
        'parent_affiliate_id',
        'is_sub_affiliate',
        'sub_affiliate_permissions',
        'require_onboarding_form',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_active' => 'boolean',
        'is_sub_affiliate' => 'boolean',
        'require_onboarding_form' => 'boolean',
        'sub_affiliate_permissions' => 'array',
        'wallet_balance' => 'decimal:2',
        'default_commission_percentage' => 'decimal:2',
        'commission_hold_days' => 'integer',
        'gallery_images' => 'array',
        'show_location_section' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Affiliate::class, 'parent_affiliate_id');
    }

    public function subAffiliates()
    {
        return $this->hasMany(Affiliate::class, 'parent_affiliate_id');
    }

    public function isSubAffiliate(): bool
    {
        return (bool) $this->is_sub_affiliate || !empty($this->parent_affiliate_id);
    }

    public function hasSubPermission(string $key): bool
    {
        if (!$this->isSubAffiliate()) {
            return true;
        }
        $perms = $this->sub_affiliate_permissions ?? [];
        if (!array_key_exists($key, $perms)) {
            return true;
        }
        return (bool) $perms[$key];
    }

    public function getCommissionTargetAffiliate(): self
    {
        if ($this->isSubAffiliate() && $this->parent) {
            return $this->parent;
        }
        return $this;
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approved_by_user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function rejected_by_user()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function affiliatePackages()
    {
        return $this->hasMany(AffiliatePackage::class);
    }

    public function affiliateWebsites()
    {
        return $this->hasMany(AffiliateWebsite::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'affiliate_packages')
            ->withPivot(['website_id', 'commission_percentage', 'is_active'])
            ->withTimestamps();
    }

    public function walletTransactions()
    {
        return $this->hasMany(AffiliateWalletTransaction::class);
    }

    public function withdrawPayoutMethods()
    {
        return $this->hasMany(WithdrawPayoutMethod::class, 'owner_id')
            ->where('owner_type', 'affiliate');
    }

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'owner_id')
            ->where('owner_type', 'affiliate');
    }

    public function w9Form()
    {
        return $this->hasOne(W9Form::class);
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name ?: 'affiliate');
        $slug = $base;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
