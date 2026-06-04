<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AffiliateWalletTransaction extends Model
{
    protected $fillable = [
        'affiliate_id',
        'transaction_id',
        'type',
        'status',
        'amount',
        'balance_after',
        'description',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'meta' => 'array',
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
