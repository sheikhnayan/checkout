<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'hero_title',
        'hero_subtitle',
        'date', 
        'address',
        'image',
        'gallery_images',
        'description',
        'secondary_description',
        'website_id',
        'logo_width',
        'logo_height'
    ];

    protected $casts = [
        'gallery_images' => 'array',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class, 'event_id', 'id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function feedModels()
    {
        return $this->belongsToMany(FeedModel::class, 'feed_model_event', 'event_id', 'feed_model_id')
            ->withTimestamps();
    }
}
