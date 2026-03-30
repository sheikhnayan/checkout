<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedModel extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'profile_image',
        'bio',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function posts()
    {
        return $this->hasMany(FeedPost::class)->latest('posted_at');
    }

    public function performanceDates()
    {
        return $this->hasMany(FeedModelPerformanceDate::class)->orderBy('performance_date');
    }
}