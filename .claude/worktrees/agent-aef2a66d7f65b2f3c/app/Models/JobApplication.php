<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_post_id',
        'website_id',
        'application_type',
        'legal_first_name',
        'legal_last_name',
        'display_first_name',
        'display_last_name',
        'email',
        'phone',
        'city',
        'state',
        'preferred_contact_method',
        'status',
        'social_handles',
        'traits',
        'skills',
        'availability',
        'positions',
        'employment_history',
        'education',
        'attachments',
        'additional_notes',
        'submitted_at',
    ];

    protected $casts = [
        'social_handles' => 'array',
        'traits' => 'array',
        'skills' => 'array',
        'availability' => 'array',
        'positions' => 'array',
        'employment_history' => 'array',
        'education' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
