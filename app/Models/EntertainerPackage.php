<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainerPackage extends Model
{
    protected $fillable = [
        'entertainer_id',
        'website_id',
        'package_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
