<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'authorize_key',
        'authorize_secret',
        'stripe_key',
        'stripe_secret',
        'payment_method',
        'sandbox_mode',
        'affiliate_withdraw_charge',
    ];

    protected $casts = [
        'affiliate_withdraw_charge' => 'decimal:2',
        'sandbox_mode' => 'boolean',
    ];
}
