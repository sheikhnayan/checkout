<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FeedModel extends Model
{
    protected $fillable = [
        'website_id',
        'slug',
        'name',
        'profile_image',
        'bio',
        'is_real_profile',
        'is_active',
    ];

    protected $casts = [
        'is_real_profile' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getProfileTypeLabelAttribute(): string
    {
        return $this->is_real_profile ? 'Real' : 'Fake';
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function posts()
    {
        return $this->hasMany(FeedPost::class)->latest('posted_at');
    }

    public function performanceDates()
    {
        return $this->hasMany(FeedModelPerformanceDate::class)->orderBy('performance_date');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (FeedModel $feedModel): void {
            if (blank($feedModel->slug)) {
                $feedModel->slug = static::generateUniqueSlug((string) $feedModel->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        if ($base === '') {
            $base = 'profile';
        }

        do {
            $slug = $base . '-' . Str::lower(Str::random(6));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }
}