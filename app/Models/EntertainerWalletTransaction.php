<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntertainerWalletTransaction extends Model
{
    protected $fillable = [
        'entertainer_id',
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

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
