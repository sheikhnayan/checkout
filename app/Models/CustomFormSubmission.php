<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_form_id',
        'website_id',
        'submitter_ip',
        'user_agent',
        'submission_data',
    ];

    protected $casts = [
        'submission_data' => 'array',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class, 'website_id');
    }
}
