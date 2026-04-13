<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WitnessReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'submitted_by_user_id',
        'submitted_via',
        'location_legal_name',
        'location_dba_name',
        'location_address',
        'incident_calendar_date',
        'date_submitted',
        'incident_time',
        'incident_type',
        'full_name',
        'address',
        'phone_number',
        'participant_type',
        'detailed_statement',
        'accepted_esignature',
        'opted_out_esignature',
        'digital_signature_name',
    ];

    protected $casts = [
        'incident_calendar_date' => 'date',
        'date_submitted' => 'date',
        'accepted_esignature' => 'boolean',
        'opted_out_esignature' => 'boolean',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    public function attachments()
    {
        return $this->hasMany(WitnessReportAttachment::class)->latest();
    }
}
