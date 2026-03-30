<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeedModelPerformanceDate extends Model
{
    protected $fillable = [
        'feed_model_id',
        'performance_date',
    ];

    protected $casts = [
        'performance_date' => 'date',
    ];

    public function feedModel()
    {
        return $this->belongsTo(FeedModel::class);
    }
}
