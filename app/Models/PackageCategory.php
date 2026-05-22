<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'icon',
        'sort_order',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'package_category_id');
    }
}