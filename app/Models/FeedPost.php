<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedPost extends Model
{
    protected $fillable = [
        'website_id',
        'feed_model_id',
        'author_mode',
        'caption',
        'images',
        'media_items',
        'is_active',
        'approval_status',
        'approved_at',
        'approved_by',
        'show_on_roll_call',
        'roll_call_date',
        'roll_call_start_date',
        'roll_call_end_date',
        'posted_at',
    ];

    protected $casts = [
        'images' => 'array',
        'media_items' => 'array',
        'is_active' => 'boolean',
        'approved_at' => 'datetime',
        'show_on_roll_call' => 'boolean',
        'roll_call_date' => 'date',
        'roll_call_start_date' => 'date',
        'roll_call_end_date' => 'date',
        'posted_at' => 'datetime',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function feedModel()
    {
        return $this->belongsTo(FeedModel::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function comments()
    {
        return $this->hasMany(FeedComment::class)->latest();
    }

    public function visibleComments()
    {
        return $this->hasMany(FeedComment::class)->where('is_visible', true)->latest();
    }

    public function getPrimaryImageAttribute(): ?string
    {
        $images = collect($this->resolved_media_items)
            ->firstWhere('type', 'image');

        if ($images) {
            return $images['url'] ?? null;
        }

        $images = array_values(array_filter((array) $this->images));

        return $images[0] ?? null;
    }

    public function getResolvedMediaItemsAttribute(): array
    {
        $items = array_values(array_filter((array) $this->media_items));

        if (!empty($items)) {
            return array_map(function ($item) {
                return [
                    'type' => $item['type'] ?? 'image',
                    'source' => $item['source'] ?? 'upload',
                    'url' => $item['url'] ?? null,
                ];
            }, $items);
        }

        return array_map(function ($path) {
            return [
                'type' => 'image',
                'source' => 'upload',
                'url' => $path,
            ];
        }, array_values(array_filter((array) $this->images)));
    }

    public function getAuthorNameAttribute(): string
    {
        if ($this->author_mode === 'club' || !$this->feedModel) {
            return $this->website->name ?? 'Club';
        }

        return $this->feedModel->name;
    }

    public function getAuthorAvatarAttribute(): ?string
    {
        if ($this->author_mode === 'club' || !$this->feedModel) {
            return $this->website->logo ?? null;
        }

        return $this->feedModel->profile_image;
    }
}
