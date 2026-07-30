<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLogo extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'logo',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
