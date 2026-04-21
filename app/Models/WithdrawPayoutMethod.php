<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawPayoutMethod extends Model
{
    protected $fillable = [
        'owner_id',
        'owner_type',
        'label',
        'type',
        'details',
        'is_default',
    ];

    protected $casts = [
        'details'    => 'array',
        'is_default' => 'boolean',
    ];

    // ------------------------------------------------------------------ helpers

    /**
     * Readable type labels.
     */
    public static function typeLabels(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer (ACH)',
            'wire'          => 'Wire Transfer',
            'check'         => 'Check',
            'paypal'        => 'PayPal',
            'zelle'         => 'Zelle',
            'other'         => 'Other',
        ];
    }

    public function typeLabel(): string
    {
        return static::typeLabels()[$this->type] ?? ucfirst($this->type);
    }

    // ------------------------------------------------------------------ scopes

    public function scopeForOwner($query, int $ownerId, string $ownerType)
    {
        return $query->where('owner_id', $ownerId)->where('owner_type', $ownerType);
    }
}
