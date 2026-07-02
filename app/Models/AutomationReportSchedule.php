<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutomationReportSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'name',
        'frequency',
        'report_period_type',
        'website_ids',
        'email_recipients',
        'timezone',
        'send_time',
        'weekly_day',
        'monthly_day',
        'yearly_month',
        'yearly_day',
        'custom_from_month',
        'custom_to_month',
        'one_time_date',
        'one_time_time',
        'is_active',
        'next_run_at',
        'last_run_at',
        'last_run_status',
        'last_error',
    ];

    protected $casts = [
        'website_ids' => 'array',
        'email_recipients' => 'array',
        'custom_from_month' => 'date',
        'custom_to_month' => 'date',
        'one_time_date' => 'date',
        'is_active' => 'boolean',
        'next_run_at' => 'datetime',
        'last_run_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function runs()
    {
        return $this->hasMany(AutomationReportRun::class)->latest('id');
    }
}
