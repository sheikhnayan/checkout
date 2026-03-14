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
        'description',
        'secondary_description',
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
        'wallet_balance',
        'approved_at',
        'approved_by',
        'rejection_reason',
        'is_active',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'is_active' => 'boolean',
        'wallet_balance' => 'decimal:2',
        'default_commission_percentage' => 'decimal:2',
        'gallery_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
