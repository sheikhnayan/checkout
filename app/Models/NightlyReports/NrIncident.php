<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrIncident extends Model
{
    protected $table = 'nr_incidents';

    protected $fillable = [
        'location_id',
        'incident_date',
        'time_of_incident',
        'report_type_field',
        'submitter_name',
        'gm_email',
        'managers_on_duty',
        'manager_phone',
        'cast_members_on_duty',
        'involved_persons',
        'incident_description',
        'witnesses',
        'police_report_number',
        'police_officers_badges',
        'police_report_file',
        'camera_angles',
        'camera_timestamp',
        'additional_footage_info',
        'additional_footage_file',
        'restricted',
        'status',
        'e_signature',
        'created_by_user_id',
    ];

    protected $casts = [
        'incident_date' => 'date',
        'restricted' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(NrLocation::class, 'location_id');
    }

    public function witnessStatements()
    {
        return $this->hasMany(NrWitnessStatement::class, 'incident_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
