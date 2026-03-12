<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
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

    public function category()
    {
        return $this->belongsTo(PackageCategory::class, 'package_category_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
