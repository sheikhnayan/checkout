<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SharedCart extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'cart_data',
        'website_slug',
    ];

    protected $casts = [
        'cart_data' => 'array',
    ];
}
