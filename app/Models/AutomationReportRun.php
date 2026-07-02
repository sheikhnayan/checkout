<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationReportRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'automation_report_schedule_id',
        'triggered_by_user_id',
        'status',
        'email_recipients',
        'website_ids',
        'report_params',
        'file_path',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'email_recipients' => 'array',
        'website_ids' => 'array',
        'report_params' => 'array',
        'sent_at' => 'datetime',
    ];

    public function schedule()
    {
        return $this->belongsTo(AutomationReportSchedule::class, 'automation_report_schedule_id');
    }

    public function triggeredBy()
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }
}
