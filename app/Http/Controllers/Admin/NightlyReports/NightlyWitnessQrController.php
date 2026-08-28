<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;

class NightlyWitnessQrController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();

        return view('admin.nightly-reports.witness-qr.index', compact('locations'));
    }

    public function sendQrEmail(Request $request)
    {
        $locationId = (int) $request->input('location_id');
        $loc = $this->accessibleLocations()->firstWhere('id', $locationId);
        abort_unless($loc, 404);

        return back()->with('success', "Witness QR Code package dispatched to {$loc->gm_email} for {$loc->name}.");
    }
}
