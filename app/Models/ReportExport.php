<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_id',
        'format',
        'filters',
        'file_path',
        'status',
        'email_recipient',
        'error_message',
        'scheduled_at',
        'exported_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'scheduled_at' => 'datetime',
        'exported_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
