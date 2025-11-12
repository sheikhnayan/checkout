<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'website_id',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the website associated with the user.
     */
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if the user is a website user.
     */
    public function isWebsiteUser()
    {
        return $this->user_type === 'website_user';
    }

    /**
     * Get transactions for the user's website (if website user).
     */
    public function getAccessibleTransactions()
    {
        if ($this->isAdmin()) {
            return Transaction::all();
        }
        
        if ($this->isWebsiteUser() && $this->website_id) {
            return Transaction::whereHas('event', function($query) {
                $query->where('website_id', $this->website_id);
            })->get();
        }

        return collect();
    }
}
