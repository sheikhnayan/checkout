<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entertainer extends Model
{
    protected $fillable = [
        'user_id',
        'website_id',
        'feed_model_id',
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
        'default_commission_percentage' => 'decimal:2',
        'wallet_balance' => 'decimal:2',
        'gallery_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function feedModel()
    {
        return $this->belongsTo(FeedModel::class, 'feed_model_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function entertainerPackages()
    {
        return $this->hasMany(EntertainerPackage::class);
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'entertainer_packages')
            ->withPivot(['website_id', 'is_active'])
            ->withTimestamps();
    }

    public function walletTransactions()
    {
        return $this->hasMany(EntertainerWalletTransaction::class);
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name ?: 'entertainer');
        $slug = $base;
        $counter = 1;

        while (self::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
