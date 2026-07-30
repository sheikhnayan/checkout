<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CheckoutPopup extends Model
{
    protected $fillable = [
        'website_id',
        'title',
        'message',
        'image_path',
        'button_text',
        'button_url',
        'starts_at',
        'ends_at',
        'show_once_per_session',
        'is_active',
        'is_archieved',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'show_once_per_session' => 'boolean',
        'is_active' => 'boolean',
        'is_archieved' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function scopeActiveForCheckout(Builder $query, int $websiteId): Builder
    {
        $now = now();

        return $query
            ->where('website_id', $websiteId)
            ->where('is_archieved', false)
            ->where('is_active', true)
            ->where(function (Builder $inner) use ($now) {
                $inner->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
            })
            ->where(function (Builder $inner) use ($now) {
                $inner->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            });
    }
}
