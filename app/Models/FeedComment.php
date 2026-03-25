<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedComment extends Model
{
    protected $fillable = [
        'feed_post_id',
        'commenter_name',
        'commenter_email',
        'body',
        'ip_address',
        'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    public function post()
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }
}