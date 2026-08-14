<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpCenterCollaborator extends Model
{
    use HasFactory;

    protected $fillable = [
        'help_center_page_id',
        'user_id',
        'invited_by_user_id',
        'email',
        'status',
        'invitation_token',
    ];

    public function page()
    {
        return $this->belongsTo(HelpCenterPage::class, 'help_center_page_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }
}
