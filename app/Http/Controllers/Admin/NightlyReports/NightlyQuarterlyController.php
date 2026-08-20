<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrLocation;
use Carbon\Carbon;

class NightlyQuarterlyController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $year = (int) $request->input('year', Carbon::now()->year);
        $quarterGrid = [];

        foreach ($locations as $loc) {
            $q1 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereYear('business_date', $year)
                ->whereMonth('business_date', '>=', 1)
                ->whereMonth('business_date', '<=', 3)
                ->sum('net_sales');

            $q2 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereYear('business_date', $year)
                ->whereMonth('business_date', '>=', 4)
                ->whereMonth('business_date', '<=', 6)
                ->sum('net_sales');

            $q3 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereYear('business_date', $year)
                ->whereMonth('business_date', '>=', 7)
                ->whereMonth('business_date', '<=', 9)
                ->sum('net_sales');

            $q4 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereYear('business_date', $year)
                ->whereMonth('business_date', '>=', 10)
                ->whereMonth('business_date', '<=', 12)
                ->sum('net_sales');

            $total = $q1 + $q2 + $q3 + $q4;

            $quarterGrid[] = [
                'location_id' => $loc->id,
                'location_name' => $loc->name,
                'q1' => $q1,
                'q2' => $q2,
                'q3' => $q3,
                'q4' => $q4,
                'total' => $total,
            ];
        }

        return view('admin.nightly-reports.quarterly.index', compact('quarterGrid', 'locations', 'year'));
    }
}
