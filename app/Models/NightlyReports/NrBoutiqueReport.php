<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrBoutiqueReport extends Model
{
    protected $table = 'nr_boutique_reports';

    protected $fillable = [
        'location_id',
        'business_date',
        'submitter_name',
        'submitter_email',
        'gross_daily_sales',
        'daily_sales_goal',
        'total_guest_count',
        'guest_average_ticket',
        'arcade_theater_guest_count',
        'current_week_total_sales',
        'last_year_daily_sales',
        'last_year_guest_count',
        'last_year_guest_average_ticket',
        'total_returns',
        'total_discount',
        'total_payouts',
        'atm_payouts',
        'gift_cards_sold',
        'beginning_safe_balance',
        'ending_safe_balance',
        'said_deposit',
        'actual_deposit',
        'sales_direction',
        'sales_direction_reason',
        'incident_flag',
        'super_star_nomination',
        'daytime_shift_notes',
        'nighttime_shift_notes',
        'weather',
        'social_media_content',
        'ordering_notes',
        'passes_distributed',
        'is_viewed',
        'source',
        'created_by_user_id',
    ];

    protected $casts = [
        'business_date' => 'date',
        'gross_daily_sales' => 'decimal:2',
        'daily_sales_goal' => 'decimal:2',
        'total_guest_count' => 'integer',
        'guest_average_ticket' => 'decimal:2',
        'arcade_theater_guest_count' => 'integer',
        'current_week_total_sales' => 'decimal:2',
        'last_year_daily_sales' => 'decimal:2',
        'last_year_guest_count' => 'integer',
        'last_year_guest_average_ticket' => 'decimal:2',
        'total_returns' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_payouts' => 'decimal:2',
        'atm_payouts' => 'decimal:2',
        'gift_cards_sold' => 'decimal:2',
        'beginning_safe_balance' => 'decimal:2',
        'ending_safe_balance' => 'decimal:2',
        'said_deposit' => 'decimal:2',
        'actual_deposit' => 'decimal:2',
        'incident_flag' => 'boolean',
        'is_viewed' => 'boolean',
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
