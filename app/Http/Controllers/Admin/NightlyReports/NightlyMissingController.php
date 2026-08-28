<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrLocation;
use Carbon\Carbon;

class NightlyMissingController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $daysBack = (int) $request->input('days', 7);
        $missingList = [];

        for ($i = 1; $i <= $daysBack; $i++) {
            $checkDate = Carbon::now()->subDays($i)->toDateString();

            foreach ($locations as $loc) {
                $hasReport = NrNightlyReport::where('location_id', $loc->id)
                    ->where('business_date', $checkDate)
                    ->exists();

                if (!$hasReport) {
                    $missingList[] = [
                        'location_id' => $loc->id,
                        'location_name' => $loc->name,
                        'location_type' => $loc->type,
                        'business_date' => $checkDate,
                        'days_ago' => $i,
                        'gm_name' => $loc->gm_name,
                        'gm_email' => $loc->gm_email,
                        'phone' => $loc->phone,
                    ];
                }
            }
        }

        return view('admin.nightly-reports.missing.index', compact('missingList', 'daysBack', 'locations'));
    }

    public function sendReminder(Request $request)
    {
        $locationId = (int) $request->input('location_id');
        $date = $request->input('business_date');

        $loc = $this->accessibleLocations()->firstWhere('id', $locationId);
        abort_unless($loc, 404);

        // Simulated notification trigger
        return back()->with('success', "Reminder notification sent to GM for {$loc->name} for date {$date}.");
    }
}
