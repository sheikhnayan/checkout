<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'date', 
        'address',
        'image',
        'description',
        'website_id',
        'logo_width',
        'logo_height'
    ];

    public function packages()
    {
        return $this->hasMany(Package::class, 'event_id', 'id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
