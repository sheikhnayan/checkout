<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPreferenceRequest extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'email',
        'phone',
        'city',
        'state',
        'preferred_role',
        'availability',
        'social_handles',
        'experience',
        'attachments',
        'message',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'availability' => 'array',
        'social_handles' => 'array',
        'experience' => 'array',
        'attachments' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }
}
