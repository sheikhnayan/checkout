<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrHighTransaction extends Model
{
    protected $table = 'nr_high_transactions';

    protected $fillable = [
        'location_id',
        'transaction_date',
        'customer_name',
        'customer_phone',
        'customer_email',
        'card_last4',
        'card_brand',
        'amount',
        'authorizing_manager_name',
        'id_image',
        'card_image',
        'receipt_image',
        'notes',
        'created_by_user_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
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
