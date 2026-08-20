<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrLegalToken extends Model
{
    protected $table = 'nr_legal_tokens';

    protected $fillable = [
        'token',
        'attorney_name',
        'firm_name',
        'case_reference',
        'location_ids',
        'incident_ids',
        'expires_at',
        'revoked',
        'created_by_user_id',
    ];

    protected $casts = [
        'location_ids' => 'array',
        'incident_ids' => 'array',
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function isValid(): bool
    {
        if ($this->revoked) {
            return false;
        }
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }
        return true;
    }
}
