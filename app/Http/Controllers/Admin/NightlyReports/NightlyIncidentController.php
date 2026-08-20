<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrIncident;
use App\Models\NightlyReports\NrLocation;

class NightlyIncidentController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = NrIncident::with(['location', 'witnessStatements'])
            ->whereIn('location_id', $allowedLocationIds);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('submitter_name', 'like', "%{$search}%")
                    ->orWhere('report_type_field', 'like', "%{$search}%")
                    ->orWhere('incident_description', 'like', "%{$search}%")
                    ->orWhere('police_report_number', 'like', "%{$search}%")
                    ->orWhere('involved_persons', 'like', "%{$search}%");
            });
        }

        $incidents = $query->orderByDesc('incident_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.nightly-reports.incidents.index', compact(
            'incidents',
            'locations',
            'selectedLocationId',
            'status',
            'search'
        ));
    }

    public function show($id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $incident = NrIncident::with(['location', 'witnessStatements'])
            ->whereIn('location_id', $allowedLocationIds)
            ->findOrFail($id);

        return view('admin.nightly-reports.incidents.show', compact('incident'));
    }

    public function updateStatus(Request $request, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $incident = NrIncident::whereIn('location_id', $allowedLocationIds)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,under_review,legal_hold,resolved',
            'restricted' => 'nullable|boolean',
        ]);

        $incident->update([
            'status' => $validated['status'],
            'restricted' => $request->has('restricted') ? (bool) $request->input('restricted') : $incident->restricted,
        ]);

        return back()->with('success', 'Incident status updated.');
    }
}
