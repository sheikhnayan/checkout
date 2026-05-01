<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'status',
        'website_id',
        'audience',
        'affiliate_id',
        'entertainer_id',
        'number_of_guest',
        'guest_limit_type',
        'package_type',
        'daily_ticket_limit',
        'daily_table_limit',
        'guests_per_table',
        'multiple',
        'transportation',
        'package_category_id',
        'event_id',
        'is_archieved',
    ];
    
    public const AUDIENCE_CLUB = 'club';
    public const AUDIENCE_AFFILIATE = 'affiliate';
    public const AUDIENCE_ENTERTAINER = 'entertainer';
    
    public const ALLOWED_AUDIENCES = [
        self::AUDIENCE_CLUB,
        self::AUDIENCE_AFFILIATE,
        self::AUDIENCE_ENTERTAINER,
    ];

    /**
     * Get the addons for the package.
     */
    public function addons()
    {
        return $this->hasMany(Addon::class);
    }

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

    public function category()
    {
        return $this->belongsTo(PackageCategory::class, 'package_category_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    
    public function scopeClubVisible($query)
    {
        return $query->where(function ($builder) {
            $builder->whereNull('audience')
                ->orWhere('audience', self::AUDIENCE_CLUB);
        });
    }
}
