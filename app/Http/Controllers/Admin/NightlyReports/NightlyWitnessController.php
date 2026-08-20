<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrWitnessStatement;
use App\Models\NightlyReports\NrIncident;
use App\Models\NightlyReports\NrLocation;

class NightlyWitnessController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $search = $request->input('search');

        $query = NrWitnessStatement::with(['location', 'incident'])
            ->whereIn('location_id', $allowedLocationIds);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('witness_name', 'like', "%{$search}%")
                    ->orWhere('witness_email', 'like', "%{$search}%")
                    ->orWhere('witness_phone', 'like', "%{$search}%")
                    ->orWhere('statement_text', 'like', "%{$search}%");
            });
        }

        $statements = $query->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $availableIncidents = NrIncident::whereIn('location_id', $allowedLocationIds)
            ->latest('incident_date')
            ->take(50)
            ->get();

        return view('admin.nightly-reports.witness.index', compact(
            'statements',
            'locations',
            'selectedLocationId',
            'search',
            'availableIncidents'
        ));
    }

    public function linkIncident(Request $request, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $statement = NrWitnessStatement::whereIn('location_id', $allowedLocationIds)->findOrFail($id);

        $incidentId = $request->input('incident_id');
        $statement->incident_id = $incidentId ?: null;
        $statement->save();

        return back()->with('success', 'Witness statement linked to incident.');
    }
}
