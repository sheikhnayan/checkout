<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserReportPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_id',
        'name',
        'filters',
        'columns',
        'is_favorite',
        'last_run_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'columns' => 'array',
        'is_favorite' => 'boolean',
        'last_run_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
