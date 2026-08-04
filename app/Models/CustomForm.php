<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'updated_by_user_id',
        'title',
        'slug',
        'description',
        'website_ids',
        'is_active',
        'fields_schema',
        'settings',
    ];

    protected $casts = [
        'website_ids' => 'array',
        'fields_schema' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function submissions()
    {
        return $this->hasMany(CustomFormSubmission::class)->latest('id');
    }

    public function activityLogs()
    {
        return $this->hasMany(CustomFormActivityLog::class)->latest('id');
    }

    public function getPublicUrlAttribute(): string
    {
        return route('forms.public.show', $this->slug);
    }
}
