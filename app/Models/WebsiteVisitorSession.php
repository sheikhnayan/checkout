<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteVisitorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'affiliate_id',
        'entertainer_id',
        'channel_source',
        'session_id',
        'visitor_key',
        'landing_path',
        'referrer_host',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'ip_address',
        'user_agent',
        'page_views',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'page_views' => 'integer',
        'affiliate_id' => 'integer',
        'entertainer_id' => 'integer',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function scopeForWebsite($query, $websiteId)
    {
        if (is_array($websiteId) || $websiteId instanceof \Illuminate\Support\Collection) {
            return $query->whereIn('website_id', $websiteId);
        }
        return $query->where('website_id', $websiteId);
    }

    public function scopeForAffiliate($query, $affiliateId)
    {
        if (is_array($affiliateId) || $affiliateId instanceof \Illuminate\Support\Collection) {
            return $query->whereIn('affiliate_id', $affiliateId);
        }
        return $query->where('affiliate_id', $affiliateId);
    }

    public function scopeDirect($query)
    {
        return $query->whereNull('affiliate_id')->whereNull('entertainer_id');
    }
}

