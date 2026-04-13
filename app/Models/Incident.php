<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'public_witness_token',
        'status',
        'status_changed_at',
        'status_changed_by_user_id',
        'location_legal_name',
        'location_dba_name',
        'location_address',
        'incident_calendar_date',
        'date_submitted',
        'incident_time',
        'incident_type',
        'police_report_number',
        'police_officers_badges',
        'reporter_name',
        'managers_on_duty',
        'manager_phone',
        'involved_injured_persons',
        'incident_description',
        'witnesses_statement',
        'camera_angles',
        'camera_timestamp',
        'cast_members_involved',
        'additional_media_notes',
        'accepted_esignature',
        'opted_out_esignature',
        'digital_signature_name',
        'created_by_user_id',
    ];

    protected $casts = [
        'incident_calendar_date' => 'date',
        'date_submitted' => 'date',
        'status_changed_at' => 'datetime',
        'accepted_esignature' => 'boolean',
        'opted_out_esignature' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function statusChangedBy()
    {
        return $this->belongsTo(User::class, 'status_changed_by_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(IncidentAttachment::class);
    }

    public function witnessReports()
    {
        return $this->hasMany(WitnessReport::class)->latest();
    }

    public function auditLogs()
    {
        return $this->hasMany(IncidentAuditLog::class)->latest();
    }
}
