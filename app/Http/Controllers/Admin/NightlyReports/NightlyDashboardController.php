<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrBoutiqueReport;
use App\Models\NightlyReports\NrCohReport;
use App\Models\NightlyReports\NrIncident;
use App\Models\NightlyReports\NrWitnessStatement;
use App\Models\NightlyReports\NrQuote;
use App\Models\NightlyReports\NrLocation;
use Carbon\Carbon;

class NightlyDashboardController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        if ($selectedLocationId && !in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $selectedLocationId = null;
        }

        // Date selection
        $dateRange = $request->input('date_range', 'yesterday');
        $customStart = $request->input('start_date');
        $customEnd = $request->input('end_date');

        $now = Carbon::now();
        switch ($dateRange) {
            case 'today':
                $startDate = $now->copy()->toDateString();
                $endDate = $now->copy()->toDateString();
                break;
            case 'last_7_days':
                $startDate = $now->copy()->subDays(6)->toDateString();
                $endDate = $now->copy()->toDateString();
                break;
            case 'mtd':
                $startDate = $now->copy()->startOfMonth()->toDateString();
                $endDate = $now->copy()->toDateString();
                break;
            case 'ytd':
                $startDate = $now->copy()->startOfYear()->toDateString();
                $endDate = $now->copy()->toDateString();
                break;
            case 'custom':
                $startDate = $customStart ?: $now->copy()->subDay()->toDateString();
                $endDate = $customEnd ?: $now->copy()->subDay()->toDateString();
                break;
            case 'yesterday':
            default:
                $startDate = $now->copy()->subDay()->toDateString();
                $endDate = $now->copy()->subDay()->toDateString();
                break;
        }

        // 1. KPI Stats Query
        $reportsQuery = NrNightlyReport::whereIn('location_id', $allowedLocationIds)
            ->whereBetween('business_date', [$startDate, $endDate]);

        if ($selectedLocationId) {
            $reportsQuery->where('location_id', (int) $selectedLocationId);
        }

        $totalNetSales = (float) $reportsQuery->sum('net_sales');
        $priorYearNetSales = (float) $reportsQuery->sum('last_year_net_sales');
        $totalGuests = (int) $reportsQuery->sum('total_guests');
        $totalPayouts = (float) $reportsQuery->sum('total_payouts');
        $totalDeposits = (float) $reportsQuery->sum('deposit');
        $safeBalance = (float) $reportsQuery->latest('business_date')->value('safe_balance') ?? 0;

        $guestAverage = $totalGuests > 0 ? ($totalNetSales / $totalGuests) : 0;
        $yoyGrowthPct = $priorYearNetSales > 0 ? (($totalNetSales - $priorYearNetSales) / $priorYearNetSales) * 100 : 0;

        // 2. Break-Even Pace Meter (MTD)
        $mtdStart = $now->copy()->startOfMonth()->toDateString();
        $mtdEnd = $now->copy()->toDateString();

        $mtdSalesQuery = NrNightlyReport::whereIn('location_id', $allowedLocationIds)
            ->whereBetween('business_date', [$mtdStart, $mtdEnd]);

        if ($selectedLocationId) {
            $mtdSalesQuery->where('location_id', (int) $selectedLocationId);
            $totalBreakEven = (float) NrLocation::where('id', (int) $selectedLocationId)->value('break_even') ?? 0;
        } else {
            $totalBreakEven = (float) NrLocation::whereIn('id', $allowedLocationIds)->sum('break_even');
        }

        $mtdSales = (float) $mtdSalesQuery->sum('net_sales');
        $breakEvenPacePct = $totalBreakEven > 0 ? min(100, round(($mtdSales / $totalBreakEven) * 100, 1)) : 0;

        // 3. Daily Matrix Grid for selected date
        $targetDate = $endDate;
        $dailyGrid = [];
        $activeLocations = $selectedLocationId
            ? $locations->where('id', (int) $selectedLocationId)
            : $locations;

        foreach ($activeLocations as $loc) {
            $report = NrNightlyReport::where('location_id', $loc->id)
                ->where('business_date', $targetDate)
                ->first();

            $sales = $report ? (float) $report->net_sales : 0;
            $goal = $report && $report->nightly_goal ? (float) $report->nightly_goal : ((float) $loc->nightly_goal ?? 0);
            $variance = $sales - $goal;
            $variancePct = $goal > 0 ? ($variance / $goal) * 100 : 0;
            $guests = $report ? (int) $report->total_guests : 0;
            $avgSpend = $report ? (float) $report->guest_average : ($guests > 0 ? $sales / $guests : 0);

            $dailyGrid[] = [
                'location_id' => $loc->id,
                'location_name' => $loc->name,
                'location_type' => $loc->type,
                'report_id' => $report ? $report->id : null,
                'has_report' => $report !== null,
                'net_sales' => $sales,
                'nightly_goal' => $goal,
                'variance' => $variance,
                'variance_pct' => $variancePct,
                'total_guests' => $guests,
                'guest_average' => $avgSpend,
                'met_goal' => $sales >= $goal && $goal > 0,
                'weather' => $report ? $report->weather : null,
                'incident_flag' => $report ? $report->incident_flag : false,
                'submitter_name' => $report ? $report->submitter_name : null,
            ];
        }

        // 4. Daily Quote
        $quote = NrQuote::where('active', true)->inRandomOrder()->first();

        // 5. Recent Submissions Stream
        $recentNightly = NrNightlyReport::with('location')
            ->whereIn('location_id', $allowedLocationIds)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($r) {
                return [
                    'type' => 'nightly',
                    'id' => $r->id,
                    'location_name' => $r->location->name ?? 'Venue',
                    'business_date' => $r->business_date->format('M d, Y'),
                    'submitter_name' => $r->submitter_name,
                    'summary' => '$' . number_format($r->net_sales, 2) . ' Net Sales (' . $r->total_guests . ' Guests)',
                    'created_at' => $r->created_at,
                    'is_viewed' => $r->is_viewed,
                    'url' => route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $r->id]),
                ];
            });

        $recentIncidents = NrIncident::with('location')
            ->whereIn('location_id', $allowedLocationIds)
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($inc) {
                return [
                    'type' => 'incident',
                    'id' => $inc->id,
                    'location_name' => $inc->location->name ?? 'Venue',
                    'business_date' => $inc->incident_date->format('M d, Y'),
                    'submitter_name' => $inc->submitter_name,
                    'summary' => $inc->report_type_field . ': ' . substr($inc->incident_description, 0, 60) . '...',
                    'created_at' => $inc->created_at,
                    'is_viewed' => false,
                    'url' => route('admin.nightly-reports.incidents.show', $inc->id),
                ];
            });

        $recentSubmissions = $recentNightly->concat($recentIncidents)
            ->sortByDesc('created_at')
            ->take(12)
            ->values();

        return view('admin.nightly-reports.dashboard', compact(
            'locations',
            'selectedLocationId',
            'dateRange',
            'startDate',
            'endDate',
            'totalNetSales',
            'priorYearNetSales',
            'yoyGrowthPct',
            'totalGuests',
            'guestAverage',
            'totalPayouts',
            'totalDeposits',
            'safeBalance',
            'mtdSales',
            'totalBreakEven',
            'breakEvenPacePct',
            'dailyGrid',
            'quote',
            'recentSubmissions'
        ));
    }
}
