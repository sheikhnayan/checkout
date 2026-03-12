<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    protected $fillable = [
        'website_id',
        'name',
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