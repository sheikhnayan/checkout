<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliatePackage extends Model
{
    protected $fillable = [
        'affiliate_id',
        'website_id',
        'package_id',
        'commission_percentage',
        'is_active',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
