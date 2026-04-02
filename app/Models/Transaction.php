<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Affiliate;
use App\Models\Entertainer;

class Transaction extends Model
{
    protected $casts = [
        'cart_items' => 'array',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
