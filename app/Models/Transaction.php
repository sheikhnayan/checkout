<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Affiliate;

class Transaction extends Model
{
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
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
