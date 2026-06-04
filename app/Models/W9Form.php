<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class W9Form extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'affiliate_id',
        'entertainer_id',
        'type',
        'full_name',
        'business_name',
        'tax_classification',
        'tax_classification_other',
        'tax_id_type',
        'tax_id_number',
        'street_address',
        'city',
        'state',
        'zip_code',
        'account_numbers',
        'requester_name',
        'requester_phone',
        'requester_email',
        'certification_signed',
        'certification_date',
        'certification_ip',
        'exempt_payee_code',
        'fatca_exemption_code',
        'id_front_image',
        'id_back_image',
        'id_document_type',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'certification_signed' => 'boolean',
        'certification_date' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function promoter()
    {
        return $this->belongsTo(Promoter::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getRelatedModel()
    {
        return $this->type === 'promoter' ? $this->promoter : $this->entertainer;
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isSubmitted()
    {
        return $this->status === 'submitted';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
