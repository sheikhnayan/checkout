<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WitnessReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'witness_report_id',
        'attachment_type',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
    ];

    public function witnessReport()
    {
        return $this->belongsTo(WitnessReport::class);
    }
}
