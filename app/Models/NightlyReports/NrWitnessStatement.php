<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrWitnessStatement extends Model
{
    protected $table = 'nr_witness_statements';

    protected $fillable = [
        'incident_id',
        'location_id',
        'incident_date',
        'time_of_incident',
        'type_of_incident',
        'witness_name',
        'witness_address',
        'witness_phone',
        'witness_email',
        'witness_type',
        'statement_text',
        'media_attachment',
        'submitter_email',
        'e_signature',
        'created_by_user_id',
    ];

    protected $casts = [
        'incident_date' => 'date',
    ];

    public function location()
    {
        return $this->belongsTo(NrLocation::class, 'location_id');
    }

    public function incident()
    {
        return $this->belongsTo(NrIncident::class, 'incident_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
