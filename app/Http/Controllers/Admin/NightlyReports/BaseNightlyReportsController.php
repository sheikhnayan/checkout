<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;
use Illuminate\Support\Facades\Auth;

class BaseNightlyReportsController extends Controller
{
    /**
     * Get accessible locations for the current authenticated user.
     */
    protected function accessibleLocations()
    {
        $user = Auth::guard('ambassador')->user() ?: Auth::user();
        if (!$user) {
            return NrLocation::whereRaw('1=0')->get();
        }

        if ($ambassador = Auth::guard('ambassador')->user()) {
            return NrLocation::whereIn('website_id', $ambassador->clubs()->pluck('websites.id'))
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return NrLocation::where('active', true)->orderBy('name')->get();
        }

        $locationIds = $user->accessibleNrLocationIds();
        return NrLocation::whereIn('id', $locationIds)
            ->where('active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get accessible location IDs for the current authenticated user.
     */
    protected function accessibleLocationIds(): array
    {
        $ambassadorGuard = Auth::guard('ambassador');
        $ambassador = $ambassadorGuard->user();
        $user = $ambassador ?: Auth::user();
        if (!$user) {
            return [];
        }

        if ($ambassador) {
            return NrLocation::whereIn('website_id', $ambassador->clubs()->pluck('websites.id'))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return NrLocation::pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $user->accessibleNrLocationIds();
    }

    /**
     * Apply location scope to any query builder instance.
     */
    protected function scopeLocation($query, $column = 'location_id', $selectedLocationId = null)
    {
        $allowedIds = $this->accessibleLocationIds();

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedIds, true)) {
            return $query->where($column, (int) $selectedLocationId);
        }

        return $query->whereIn($column, $allowedIds);
    }
}
