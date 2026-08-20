<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrDocumentRequest extends Model
{
    protected $table = 'nr_document_requests';

    protected $fillable = [
        'report_type',
        'report_id',
        'requester_id',
        'requester_name',
        'requester_email',
        'requester_role',
        'requested_for',
        'requester_note',
        'status',
        'reviewed_at',
        'reviewed_by',
        'reviewer_note',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }
}
