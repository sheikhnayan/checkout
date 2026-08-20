<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrModelRelease extends Model
{
    protected $table = 'nr_model_releases';

    protected $fillable = [
        'location_id',
        'performer_legal_name',
        'stage_name',
        'date_of_birth',
        'ssn_last4',
        'phone',
        'email',
        'address',
        'shoot_date',
        'photographer_name',
        'id_attachment',
        'release_pdf_attachment',
        'digital_signature',
        'age_verified',
        'created_by_user_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'shoot_date' => 'date',
        'age_verified' => 'boolean',
    ];

    public function location()
    {
        return $this->belongsTo(NrLocation::class, 'location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
