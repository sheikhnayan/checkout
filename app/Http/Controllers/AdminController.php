<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommissionLifecycleRunner;
use App\Models\Website;
use App\Models\Event;
use App\Models\User;
use App\Models\Transaction;
use App\Models\CustomInvoice;
use App\Models\Affiliate;
use App\Models\Entertainer;

class AdminController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index()
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $user = auth()->user();

        if ($user->isAdmin()) {
            $validTxQuery = function ($query) {
                $query->whereIn('status', [1, '1', 'completed', 'paid'])
                      ->orWhereNotIn('status', [0, '0', 'canceled', 'cancelled', 'failed', 'refunded']);
            };

            $totalRevenue = Transaction::where($validTxQuery)->sum('total');
            $monthlyRevenue = Transaction::where($validTxQuery)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('total');

            $prevMonthRevenue = Transaction::where($validTxQuery)
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->sum('total');

            $revenueGrowth = $prevMonthRevenue > 0
                ? round((($monthlyRevenue - $prevMonthRevenue) / $prevMonthRevenue) * 100, 1)
                : ($monthlyRevenue > 0 ? 100 : 0);

            $totalClubs = Website::where('is_archieved', 0)->count();
            $totalEvents = Event::count();
            $websiteUsersCount = User::where('user_type', 'website_user')->count();
            $totalTransactions = Transaction::count();
            $pendingInvoicesCount = CustomInvoice::where('status', 'sent')->count();
            $activePromotersCount = Affiliate::where('status', 'approved')->where('is_active', 1)->count();
            $activeEntertainersCount = Entertainer::where('status', 'approved')->where('is_active', 1)->count();

            // 14-day sales & volume trend for ApexCharts
            $chartDates = [];
            $chartRevenues = [];
            $chartBookings = [];

            for ($i = 13; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                $displayDate = $date->format('M d');

                $dailyStats = Transaction::whereDate('created_at', $dateStr)
                    ->selectRaw("SUM(CASE WHEN status IN (1, '1', 'completed', 'paid') OR status NOT IN (0, '0', 'canceled', 'cancelled', 'failed', 'refunded') THEN total ELSE 0 END) as revenue, COUNT(*) as count")
                    ->first();

                $chartDates[] = $displayDate;
                $chartRevenues[] = round((float) ($dailyStats->revenue ?? 0), 2);
                $chartBookings[] = (int) ($dailyStats->count ?? 0);
            }

            // Sales breakdown by Club (Top 5)
            $topClubs = Website::where('is_archieved', 0)
                ->get()
                ->map(function ($site) use ($validTxQuery) {
                    $revenue = Transaction::where($validTxQuery)
                        ->where(function ($q) use ($site) {
                            $q->whereHas('event', fn($s) => $s->where('website_id', $site->id))
                              ->orWhereHas('package', fn($s) => $s->where('website_id', $site->id))
                              ->orWhere('website_id', $site->id);
                        })->sum('total');
                    $site->calculated_revenue = (float) $revenue;
                    return $site;
                })
                ->sortByDesc('calculated_revenue')
                ->take(5)
                ->values();

            $recentTransactions = Transaction::with(['event', 'package', 'website', 'event.website', 'package.website', 'user'])
                ->latest()
                ->take(7)
                ->get();

            return view('admin.dashboard', compact(
                'totalRevenue',
                'monthlyRevenue',
                'revenueGrowth',
                'totalClubs',
                'totalEvents',
                'websiteUsersCount',
                'totalTransactions',
                'pendingInvoicesCount',
                'activePromotersCount',
                'activeEntertainersCount',
                'chartDates',
                'chartRevenues',
                'chartBookings',
                'topClubs',
                'recentTransactions'
            ));
        }

        // Website users & managers: scoped metrics
        $accessibleIds = $user->accessibleWebsiteIds();

        $allocatedWebsites = $user->isManager()
            ? $user->managedWebsites()->orderBy('name')->get()
            : ($user->website ? collect([$user->website]) : collect());

        $scopedEventCount = Event::whereIn('website_id', $accessibleIds)->count();

        $getScopedTxQuery = function () use ($accessibleIds) {
            return Transaction::where(function ($q) use ($accessibleIds) {
                $q->whereHas('event', fn($s) => $s->whereIn('website_id', $accessibleIds))
                  ->orWhereHas('package', fn($s) => $s->whereIn('website_id', $accessibleIds));
            });
        };

        $scopedTransactionCount = $getScopedTxQuery()->count();
        $scopedRevenue = $getScopedTxQuery()->where('status', 'completed')->sum('total');

        $chartDates = [];
        $chartRevenues = [];
        $chartBookings = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateStr = $date->format('Y-m-d');
            $displayDate = $date->format('M d');

            $dailyStats = $getScopedTxQuery()
                ->whereDate('created_at', $dateStr)
                ->selectRaw("SUM(CASE WHEN status = 'completed' THEN total ELSE 0 END) as revenue, COUNT(*) as count")
                ->first();

            $chartDates[] = $displayDate;
            $chartRevenues[] = round((float) ($dailyStats->revenue ?? 0), 2);
            $chartBookings[] = (int) ($dailyStats->count ?? 0);
        }

        $recentTransactions = $getScopedTxQuery()->with(['event', 'package'])->latest()->take(7)->get();

        return view('admin.dashboard', compact(
            'allocatedWebsites',
            'scopedEventCount',
            'scopedTransactionCount',
            'scopedRevenue',
            'chartDates',
            'chartRevenues',
            'chartBookings',
            'recentTransactions'
        ));
    }
}
