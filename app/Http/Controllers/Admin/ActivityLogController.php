<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Website;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Query scoped strictly to the current user's role access level
        $query = ActivityLog::with(['user', 'website'])
            ->forUserAccess($user)
            ->latest('id');

        // Apply Search Filter
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        // Apply Action Filter
        if ($request->filled('action')) {
            $query->where('action', strtolower($request->action));
        }

        // Apply Module Filter
        if ($request->filled('module')) {
            $query->where('module', strtolower($request->module));
        }

        // Apply Website Filter
        if ($request->filled('website_id')) {
            $query->where('website_id', (int)$request->website_id);
        }

        // Apply Date Range Filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate results
        $activityLogs = $query->paginate(25)->withQueryString();

        // Calculate Stats for Today (scoped)
        $todayQuery = ActivityLog::forUserAccess($user)->whereDate('created_at', today());
        $stats = [
            'total_today' => (clone $todayQuery)->count(),
            'logins_today' => (clone $todayQuery)->where('action', 'login')->count(),
            'active_users_today' => (clone $todayQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id'),
            'actions_today' => (clone $todayQuery)->whereNotIn('action', ['login', 'logout', 'failed_login'])->count(),
        ];

        // Available Websites for Filter dropdown (scoped to accessible websites)
        $accessibleWebsiteIds = $user->accessibleWebsiteIds();
        $websites = Website::whereIn('id', $accessibleWebsiteIds)->orderBy('name')->get();

        // Available Action Types for filter dropdown
        $actionTypes = [
            'login'        => 'Login',
            'logout'       => 'Logout',
            'failed_login' => 'Failed Login',
            'create'       => 'Create',
            'update'       => 'Update',
            'delete'       => 'Delete',
            'check_in'     => 'Check-In',
            'export'       => 'Export',
        ];

        return view('admin.activity_log.index', compact('activityLogs', 'stats', 'websites', 'actionTypes'));
    }
}
