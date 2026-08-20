<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrCohReport;
use App\Models\NightlyReports\NrLocation;

class NightlyCohController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = NrCohReport::with('location')
            ->whereIn('location_id', $allowedLocationIds);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('business_date', [$startDate, $endDate]);
        }

        $reports = $query->orderByDesc('business_date')
            ->paginate(20)
            ->withQueryString();

        $totalCashOnHand = (float) (clone $query)->sum('vu_cash_on_hand');
        $totalPaidOuts = (float) (clone $query)->sum('paid_outs_total');

        return view('admin.nightly-reports.coh.index', compact(
            'reports',
            'locations',
            'selectedLocationId',
            'startDate',
            'endDate',
            'totalCashOnHand',
            'totalPaidOuts'
        ));
    }
}
