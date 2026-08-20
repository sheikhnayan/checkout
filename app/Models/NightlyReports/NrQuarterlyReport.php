<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrQuarterlyReport extends Model
{
    protected $table = 'nr_quarterly_reports';

    protected $fillable = [
        'location_id',
        'year',
        'quarter',
        'net_sales',
        'total_guests',
        'guest_average',
        'prior_year_sales',
        'variance_pct',
        'strategic_notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'net_sales' => 'decimal:2',
        'total_guests' => 'integer',
        'guest_average' => 'decimal:2',
        'prior_year_sales' => 'decimal:2',
        'variance_pct' => 'decimal:2',
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
