<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobPost extends Model
{
    protected $fillable = [
        'website_id',
        'posted_by_user_id',
        'slug',
        'job_type',
        'title',
        'location',
        'employment_type',
        'compensation',
        'short_description',
        'description',
        'skills',
        'traits',
        'meta',
        'status',
        'is_archived',
    ];

    protected $casts = [
        'skills' => 'array',
        'traits' => 'array',
        'meta' => 'array',
        'status' => 'boolean',
        'is_archived' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by_user_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (JobPost $jobPost): void {
            if (blank($jobPost->slug)) {
                $jobPost->slug = static::generateUniqueSlug((string) $jobPost->title);
            }
        });
    }

    public static function generateUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'job';
        }

        do {
            $slug = $base . '-' . Str::lower(Str::random(6));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}
