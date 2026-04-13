<?php

namespace App\Models;

use LogicException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentAuditLog extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'incident_id',
        'user_id',
        'action',
        'change_summary',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'change_summary' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Incident audit logs are immutable.');
        });

        static::deleting(function () {
            throw new LogicException('Incident audit logs are immutable.');
        });
    }

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
