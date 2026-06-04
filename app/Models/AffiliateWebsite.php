<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateWebsite extends Model
{
    protected $fillable = [
        'affiliate_id',
        'website_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
