<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrCohReport extends Model
{
    protected $table = 'nr_coh_reports';

    protected $fillable = [
        'location_id',
        'business_date',
        'submitter_name',
        'submitter_email',
        'drop_safe',
        'main_safe',
        'register_1',
        'register_2',
        'register_3',
        'register_4',
        'atm_1',
        'atm_2',
        'atm_3',
        'atm_4',
        'other',
        'paid_outs_total',
        'paid_outs_explanation',
        'vu_cash_on_hand',
        'e_signature',
        'created_by_user_id',
    ];

    protected $casts = [
        'business_date' => 'date',
        'drop_safe' => 'decimal:2',
        'main_safe' => 'decimal:2',
        'register_1' => 'decimal:2',
        'register_2' => 'decimal:2',
        'register_3' => 'decimal:2',
        'register_4' => 'decimal:2',
        'atm_1' => 'decimal:2',
        'atm_2' => 'decimal:2',
        'atm_3' => 'decimal:2',
        'atm_4' => 'decimal:2',
        'other' => 'decimal:2',
        'paid_outs_total' => 'decimal:2',
        'vu_cash_on_hand' => 'decimal:2',
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
