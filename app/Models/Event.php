<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'hero_title',
        'hero_subtitle',
        'date',
        'start_date',
        'end_date',
        'event_dates',
        'show_time_range',
        'address',
        'image',
        'gallery_images',
        'description',
        'secondary_description',
        'website_id',
        'logo_width',
        'logo_height'
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'event_dates' => 'array',
        'show_time_range' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function getDateValueAttribute(): ?string
    {
        return $this->normalizeDateValue('date');
    }

    public function getStartDateValueAttribute(): ?string
    {
        return $this->normalizeDateValue('start_date');
    }

    public function getEndDateValueAttribute(): ?string
    {
        return $this->normalizeDateValue('end_date');
    }

    private function normalizeDateValue(string $attribute): ?string
    {
        $value = $this->getRawOriginal($attribute);

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return $value;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $exception) {
            return null;
        }
    }

    public function packages()
    {
        return $this->hasMany(Package::class, 'event_id', 'id');
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function feedModels()
    {
        return $this->belongsToMany(FeedModel::class, 'feed_model_event', 'event_id', 'feed_model_id')
            ->withTimestamps();
    }
}
