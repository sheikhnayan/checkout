<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageCategory extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'icon',
        'color',
        'sort_order',
        'is_archieved',
    ];

    protected $casts = [
        'is_archieved' => 'boolean',
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