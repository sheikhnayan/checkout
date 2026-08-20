<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrBoutiqueReport;
use App\Models\NightlyReports\NrLocation;

class NightlyBoutiqueController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = NrLocation::where('type', 'Boutique')->where('active', true)->get();
        $boutiqueLocationIds = $locations->pluck('id')->all();

        $selectedLocationId = $request->input('location_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = NrBoutiqueReport::with('location')
            ->whereIn('location_id', $boutiqueLocationIds);

        if ($selectedLocationId) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('business_date', [$startDate, $endDate]);
        }

        $reports = $query->orderByDesc('business_date')
            ->paginate(20)
            ->withQueryString();

        $totalSales = (float) (clone $query)->sum('gross_daily_sales');
        $totalGuests = (int) (clone $query)->sum('total_guest_count');
        $totalReturns = (float) (clone $query)->sum('total_returns');
        $totalPayouts = (float) (clone $query)->sum('total_payouts');

        return view('admin.nightly-reports.boutique.index', compact(
            'reports',
            'locations',
            'selectedLocationId',
            'startDate',
            'endDate',
            'totalSales',
            'totalGuests',
            'totalReturns',
            'totalPayouts'
        ));
    }

    public function import(Request $request)
    {
        return view('admin.nightly-reports.boutique.import');
    }
}
