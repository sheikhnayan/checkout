<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFormActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_form_id',
        'user_id',
        'action',
        'changes_summary',
        'ip_address',
    ];

    public function form()
    {
        return $this->belongsTo(CustomForm::class, 'custom_form_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
