<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NightlyTrendController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $metric = $request->input('metric', 'net_sales'); // net_sales, total_guests, guest_average, total_payouts
        $year = (int) $request->input('year', Carbon::now()->year);

        $query = NrNightlyReport::whereIn('location_id', $allowedLocationIds)
            ->whereYear('business_date', $year);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        // Monthly Aggregate for current year and prior year
        $monthlyCurrent = [];
        $monthlyPrior = [];

        for ($m = 1; $m <= 12; $m++) {
            $curSales = (float) NrNightlyReport::whereIn('location_id', $allowedLocationIds)
                ->when($selectedLocationId, fn($q) => $q->where('location_id', (int) $selectedLocationId))
                ->whereYear('business_date', $year)
                ->whereMonth('business_date', $m)
                ->sum('net_sales');

            $priorSales = (float) NrNightlyReport::whereIn('location_id', $allowedLocationIds)
                ->when($selectedLocationId, fn($q) => $q->where('location_id', (int) $selectedLocationId))
                ->whereYear('business_date', $year - 1)
                ->whereMonth('business_date', $m)
                ->sum('net_sales');

            $monthlyCurrent[] = $curSales;
            $monthlyPrior[] = $priorSales;
        }

        // 30-Day Daily Velocity
        $dailyLabels = [];
        $dailyCurrent = [];
        $dailyLastYear = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $dailyLabels[] = $date->format('M d');

            $daySales = (float) NrNightlyReport::whereIn('location_id', $allowedLocationIds)
                ->when($selectedLocationId, fn($q) => $q->where('location_id', (int) $selectedLocationId))
                ->where('business_date', $dateStr)
                ->sum('net_sales');

            $dayPrior = (float) NrNightlyReport::whereIn('location_id', $allowedLocationIds)
                ->when($selectedLocationId, fn($q) => $q->where('location_id', (int) $selectedLocationId))
                ->where('business_date', $date->copy()->subYear()->toDateString())
                ->sum('net_sales');

            $dailyCurrent[] = $daySales;
            $dailyLastYear[] = $dayPrior;
        }

        return view('admin.nightly-reports.trends.index', compact(
            'locations',
            'selectedLocationId',
            'metric',
            'year',
            'monthlyCurrent',
            'monthlyPrior',
            'dailyLabels',
            'dailyCurrent',
            'dailyLastYear'
        ));
    }
}
