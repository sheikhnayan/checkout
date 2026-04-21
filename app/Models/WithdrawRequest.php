<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $fillable = [
        'owner_id',
        'owner_type',
        'payout_method_id',
        'website_id',
        'amount',
        'fee_percentage',
        'fee_amount',
        'net_amount',
        'status',
        'notes',
        'admin_notes',
        'method_snapshot',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'fee_percentage' => 'decimal:2',
        'fee_amount'     => 'decimal:2',
        'net_amount'     => 'decimal:2',
        'method_snapshot'=> 'array',
    ];

    // ------------------------------------------------------------------ relations

    public function payoutMethod()
    {
        return $this->belongsTo(WithdrawPayoutMethod::class, 'payout_method_id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    // ------------------------------------------------------------------ helpers

    public static function statusLabels(): array
    {
        return [
            'pending'  => 'Pending',
            'done'     => 'Done',
            'rejected' => 'Rejected',
        ];
    }

    public function statusLabel(): string
    {
        return static::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'pending'  => 'bg-warning text-dark',
            'done'     => 'bg-success',
            'rejected' => 'bg-danger',
            default    => 'bg-secondary',
        };
    }

    // ------------------------------------------------------------------ scopes

    public function scopeForOwner($query, int $ownerId, string $ownerType)
    {
        return $query->where('owner_id', $ownerId)->where('owner_type', $ownerType);
    }

    public function scopeForWebsite($query, int $websiteId)
    {
        return $query->where('website_id', $websiteId);
    }
}
