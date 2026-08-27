<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NightlyReportAmbassador extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'created_by_user_id', 'is_active', 'setup_token'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function clubs()
    {
        return $this->belongsToMany(Website::class, 'ambassador_website', 'nightly_report_ambassador_id', 'website_id');
    }
}
