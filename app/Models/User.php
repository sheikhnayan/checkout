<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Affiliate;
use App\Models\Entertainer;
use App\Models\WebsiteRole;

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
        'website_role_id',
        'user_type',
        'oauth_provider',
        'oauth_provider_id',
        'avatar_url',
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

    public function websiteRole()
    {
        return $this->belongsTo(WebsiteRole::class, 'website_role_id');
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function entertainer()
    {
        return $this->hasOne(Entertainer::class);
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

    public function isAffiliate()
    {
        return $this->user_type === 'affiliate';
    }

    public function isEntertainer()
    {
        return $this->user_type === 'entertainer';
    }

    public function isBouncer()
    {
        return $this->user_type === 'bouncer';
    }

    public function isWebsiteAdmin(): bool
    {
        return (bool) optional($this->websiteRole)->is_website_admin;
    }

    public function hasRoutePermission(?string $routeName): bool
    {
        if (!$routeName) {
            return false;
        }

        if ($this->isAdmin()) {
            return true;
        }

        if (!$this->isWebsiteUser() && !$this->isBouncer()) {
            return false;
        }

        if (in_array($routeName, ['admin.index', 'admin.profile.edit', 'admin.profile.update-password'], true)) {
            return true;
        }

        $role = $this->websiteRole;
        if (!$role) {
            return false;
        }

        return $role->permissions()->where('key', $routeName)->exists();
    }

    public function firstAccessibleAdminRoute(): string
    {
        if ($this->isAdmin()) {
            return 'admin.transaction.index';
        }

        $priorityRoutes = [
            'admin.index',
            'admin.transaction.index',
            'admin.transaction.scan',
            'admin.event.index',
            'admin.package.index',
            'admin.addon.index',
            'admin.custom-invoice.index',
            'admin.jobs.index',
            'admin.feed-post.index',
            'admin.feed-model.index',
            'admin.profile.edit',
        ];

        foreach ($priorityRoutes as $routeName) {
            if ($this->hasRoutePermission($routeName)) {
                return $routeName;
            }
        }

        $firstPermission = $this->websiteRole
            ? $this->websiteRole->permissions()->orderBy('module')->orderBy('key')->first()
            : null;

        return $firstPermission?->key ?? 'admin.profile.edit';
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
