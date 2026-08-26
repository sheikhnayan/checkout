<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrNightlyReport extends Model
{
    protected $table = 'nr_nightly_reports';

    protected $fillable = [
        'location_id',
        'business_date',
        'submitter_name',
        'submitter_email',
        'additional_contributor',
        'net_sales',
        'nightly_goal',
        'last_year_net_sales',
        'weekly_running_net_sales',
        'day_shift_net_sales',
        'voids',
        'comps',
        'dance_dollars_sold',
        'dance_dollars_redeemed',
        'vip_rooms_sold',
        'total_guests',
        'paid_guests',
        'free_discount_guests',
        'passes_redeemed',
        'guest_average',
        'dance_average',
        'ipes',
        'taxi_payout',
        'atm_payout',
        'other_payouts',
        'total_payouts',
        'deposit',
        'safe_balance',
        'weather',
        'incident_flag',
        'team_member_notes',
        'ipe_notes',
        'social_media_content',
        'ordering_notes',
        'pass_distribution_locations',
        'night_summary',
        'super_star_nomination',
        'shift_comments',
        'is_viewed',
        'source',
        'created_by_user_id',
        'additional_recipient',
        'incident_notes',
        'nightly_checklists',
        'browser',
        'ip_address',
        'unique_id',
        'submission_location',
    ];

    protected $casts = [
        'business_date' => 'date',
        'net_sales' => 'decimal:2',
        'nightly_goal' => 'decimal:2',
        'last_year_net_sales' => 'decimal:2',
        'weekly_running_net_sales' => 'decimal:2',
        'day_shift_net_sales' => 'decimal:2',
        'voids' => 'decimal:2',
        'comps' => 'decimal:2',
        'dance_dollars_sold' => 'decimal:2',
        'dance_dollars_redeemed' => 'decimal:2',
        'vip_rooms_sold' => 'integer',
        'total_guests' => 'integer',
        'paid_guests' => 'integer',
        'free_discount_guests' => 'integer',
        'passes_redeemed' => 'integer',
        'guest_average' => 'decimal:2',
        'dance_average' => 'decimal:2',
        'ipes' => 'integer',
        'taxi_payout' => 'decimal:2',
        'atm_payout' => 'decimal:2',
        'other_payouts' => 'decimal:2',
        'total_payouts' => 'decimal:2',
        'deposit' => 'decimal:2',
        'safe_balance' => 'decimal:2',
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
