<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'category',
        'type',
        'available_filters',
        'default_date_range',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'available_filters' => 'array',
        'default_date_range' => 'array',
        'is_active' => 'boolean',
    ];

    public function permissions()
    {
        return $this->hasMany(ReportPermission::class);
    }

    public function userPreferences()
    {
        return $this->hasMany(UserReportPreference::class);
    }

    public function exports()
    {
        return $this->hasMany(ReportExport::class);
    }

    // Get reports accessible by current user
    public static function accessibleBy($user)
    {
        $query = static::where('is_active', true);

        // Admin can see all reports
        if ($user->user_type === 'admin') {
            return $query;
        }

        // For other users, check report permissions
        $accessibleIds = ReportPermission::where(function ($q) use ($user) {
            // By user type
            $q->where('user_type', $user->user_type)
              ->orWhereNull('user_type'); // Reports with no type restriction
        })->when($user->website_role_id, function ($q) use ($user) {
            // By website role
            $q->orWhere('website_role_id', $user->website_role_id);
        })->when($user->affiliate_id, function ($q) use ($user) {
            // For affiliates - their own reports
            $q->orWhere('affiliate_id', $user->affiliate_id);
        })->when($user->entertainer_id, function ($q) use ($user) {
            // For entertainers - their own reports
            $q->orWhere('entertainer_id', $user->entertainer_id);
        })->pluck('report_id')->unique();

        return $query->whereIn('id', $accessibleIds);
    }

    // Check if user can access this report
    public function canAccessBy($user): bool
    {
        if ($user->user_type === 'admin') {
            return true;
        }

        return ReportPermission::where('report_id', $this->id)
            ->where(function ($q) use ($user) {
                $q->where('user_type', $user->user_type)
                  ->orWhereNull('user_type');
            })
            ->orWhere(function ($q) use ($user) {
                if ($user->website_role_id) {
                    $q->where('website_role_id', $user->website_role_id);
                }
            })
            ->orWhere(function ($q) use ($user) {
                if ($user->affiliate_id) {
                    $q->where('affiliate_id', $user->affiliate_id);
                }
            })
            ->orWhere(function ($q) use ($user) {
                if ($user->entertainer_id) {
                    $q->where('entertainer_id', $user->entertainer_id);
                }
            })
            ->exists();
    }

    // Filter data based on user's scope
    public function applyUserScope($query, $user)
    {
        if ($user->user_type === 'admin') {
            return $query;
        }

        // Website-specific users see only their website's data
        if ($user->website_id) {
            $query->where('website_id', $user->website_id);
        }

        // Affiliates see only their own data
        if ($user->affiliate_id) {
            $query->where('affiliate_id', $user->affiliate_id);
        }

        // Entertainers see only their own data
        if ($user->entertainer_id) {
            $query->where('entertainer_id', $user->entertainer_id);
        }

        return $query;
    }
}
