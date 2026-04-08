<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteRole extends Model
{
    protected $fillable = [
        'website_id',
        'name',
        'slug',
        'description',
        'is_website_admin',
        'is_system',
    ];

    protected $casts = [
        'is_website_admin' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_website_role', 'website_role_id', 'permission_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'website_role_id');
    }
}
