<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
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
