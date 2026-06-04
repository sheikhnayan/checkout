<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReportPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_type',
        'website_role_id',
        'affiliate_id',
        'entertainer_id',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function websiteRole()
    {
        return $this->belongsTo(WebsiteRole::class);
    }

    public function promoter()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->belongsTo(Entertainer::class);
    }
}
