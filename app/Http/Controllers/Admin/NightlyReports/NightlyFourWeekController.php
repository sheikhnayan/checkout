<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrLocation;
use Carbon\Carbon;

class NightlyFourWeekController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $now = Carbon::now();
        $fourWeekGrid = [];

        foreach ($locations as $loc) {
            $w4 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereBetween('business_date', [$now->copy()->subDays(6)->toDateString(), $now->toDateString()])
                ->sum('net_sales');

            $w3 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereBetween('business_date', [$now->copy()->subDays(13)->toDateString(), $now->copy()->subDays(7)->toDateString()])
                ->sum('net_sales');

            $w2 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereBetween('business_date', [$now->copy()->subDays(20)->toDateString(), $now->copy()->subDays(14)->toDateString()])
                ->sum('net_sales');

            $w1 = (float) NrNightlyReport::where('location_id', $loc->id)
                ->whereBetween('business_date', [$now->copy()->subDays(27)->toDateString(), $now->copy()->subDays(21)->toDateString()])
                ->sum('net_sales');

            $avg = ($w1 + $w2 + $w3 + $w4) / 4;
            $trend = $w3 > 0 ? (($w4 - $w3) / $w3) * 100 : 0;

            $fourWeekGrid[] = [
                'location_id' => $loc->id,
                'location_name' => $loc->name,
                'location_type' => $loc->type,
                'week_1' => $w1,
                'week_2' => $w2,
                'week_3' => $w3,
                'week_4' => $w4,
                'average' => $avg,
                'trend' => $trend,
            ];
        }

        return view('admin.nightly-reports.fourweek.index', compact('fourWeekGrid', 'locations'));
    }
}
