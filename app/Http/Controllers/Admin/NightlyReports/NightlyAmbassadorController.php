<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NightlyReports\NrLocation;

class NightlyAmbassadorController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $ambassadors = User::with('nrLocations')
            ->where(function ($q) {
                $q->where('user_type', 'manager')
                    ->orWhere('user_type', 'website_user')
                    ->orWhere('user_type', 'admin')
                    ->orWhereHas('nrLocations');
            })
            ->orderBy('name')
            ->paginate(20);

        $locations = NrLocation::where('active', true)->orderBy('name')->get();

        return view('admin.nightly-reports.ambassadors.index', compact('ambassadors', 'locations'));
    }

    public function assignLocations(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $locationIds = $request->input('location_ids', []);

        $user->nrLocations()->sync($locationIds);

        return back()->with('success', "Assigned " . count($locationIds) . " clubs to {$user->name}.");
    }
}
