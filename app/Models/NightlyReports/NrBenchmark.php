<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrBenchmark extends Model
{
    protected $table = 'nr_benchmarks';

    protected $fillable = [
        'location_id',
        'historical_best',
        'break_even',
        'updated_by_user_id',
    ];

    protected $casts = [
        'historical_best' => 'decimal:2',
        'break_even' => 'decimal:2',
    ];

    public function location()
    {
        return $this->belongsTo(NrLocation::class, 'location_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }
}
