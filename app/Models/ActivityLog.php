<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_type',
        'website_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Scope query to only include activity logs accessible by the given user.
     *
     * - Super Admin: Sees all logs across all clubs/websites.
     * - Website Admin: Sees logs for users under their website ($user->website_id).
     * - Manager: Sees logs for users who have access to the same clubs ($user->accessibleWebsiteIds()).
     */
    public function scopeForUserAccess($query, ?User $user = null)
    {
        $user = $user ?: auth()->user();

        if (!$user) {
            return $query->whereRaw('1 = 0');
        }

        // Super Admin gets access to all logs
        if ($user->isAdmin()) {
            return $query;
        }

        // Manager access scoping
        if ($user->isManager()) {
            $accessibleWebsites = $user->accessibleWebsiteIds();
            if (empty($accessibleWebsites)) {
                return $query->where('user_id', $user->id);
            }

            return $query->where(function ($q) use ($accessibleWebsites, $user) {
                // Logs associated directly with their accessible websites
                $q->whereIn('website_id', $accessibleWebsites)
                  // OR logs created by users assigned to those accessible websites
                  ->orWhereHas('user', function ($uq) use ($accessibleWebsites) {
                      $uq->whereIn('website_id', $accessibleWebsites)
                         ->orWhereHas('managedWebsites', function ($mq) use ($accessibleWebsites) {
                             $mq->whereIn('websites.id', $accessibleWebsites);
                         });
                  })
                  // OR logs created by this manager user directly
                  ->orWhere('user_id', $user->id);
            });
        }

        // Website Admin / Website User access scoping
        $websiteId = $user->website_id;
        if ($websiteId) {
            return $query->where(function ($q) use ($websiteId, $user) {
                $q->where('website_id', $websiteId)
                  ->orWhereHas('user', function ($uq) use ($websiteId) {
                      $uq->where('website_id', $websiteId);
                  })
                  ->orWhere('user_id', $user->id);
            });
        }

        // Fallback for isolated users with no website association: only see own logs
        return $query->where('user_id', $user->id);
    }
}
