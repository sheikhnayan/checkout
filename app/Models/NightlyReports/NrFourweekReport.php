<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrFourweekReport extends Model
{
    protected $table = 'nr_fourweek_reports';

    protected $fillable = [
        'location_id',
        'week_ending_date',
        'week_1_sales',
        'week_2_sales',
        'week_3_sales',
        'week_4_sales',
        'four_week_average',
        'trend_pct',
        'variance_notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'week_ending_date' => 'date',
        'week_1_sales' => 'decimal:2',
        'week_2_sales' => 'decimal:2',
        'week_3_sales' => 'decimal:2',
        'week_4_sales' => 'decimal:2',
        'four_week_average' => 'decimal:2',
        'trend_pct' => 'decimal:2',
    ];

    public function location()
    {
        return $this->belongsTo(NrLocation::class, 'location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
