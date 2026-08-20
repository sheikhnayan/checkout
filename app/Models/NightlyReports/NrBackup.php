<?php

namespace App\Models\NightlyReports;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class NrBackup extends Model
{
    protected $table = 'nr_backups';

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'checksum',
        'encryption_type',
        'created_by_user_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
