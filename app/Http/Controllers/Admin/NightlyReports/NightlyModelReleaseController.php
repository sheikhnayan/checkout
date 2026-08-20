<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrModelRelease;
use App\Models\NightlyReports\NrLocation;

class NightlyModelReleaseController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $search = $request->input('search');

        $query = NrModelRelease::with('location')
            ->where(function ($q) use ($allowedLocationIds) {
                $q->whereIn('location_id', $allowedLocationIds)
                    ->orWhereNull('location_id');
            });

        if ($selectedLocationId) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('performer_legal_name', 'like', "%{$search}%")
                    ->orWhere('stage_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $releases = $query->orderByDesc('shoot_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.nightly-reports.model-releases.index', compact(
            'releases',
            'locations',
            'selectedLocationId',
            'search'
        ));
    }

    public function show($id)
    {
        $release = NrModelRelease::with('location')->findOrFail($id);
        return response()->json($release);
    }
}
