<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;
use App\Models\NightlyReports\NrBenchmark;
use App\Models\Website;

class NightlyLocationController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = NrLocation::with('website')
            ->orderBy('name')
            ->get();

        $websites = Website::orderBy('website_name')->get();

        return view('admin.nightly-reports.locations.index', compact('locations', 'websites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|string',
            'website_id' => 'nullable|exists:websites,id',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'gm_name' => 'nullable|string|max:100',
            'gm_email' => 'nullable|email|max:150',
            'nightly_goal' => 'nullable|numeric|min:0',
            'break_even' => 'nullable|numeric|min:0',
            'historical_best' => 'nullable|numeric|min:0',
        ]);

        $loc = NrLocation::create($validated);

        NrBenchmark::updateOrCreate(
            ['location_id' => $loc->id],
            [
                'historical_best' => $validated['historical_best'] ?? null,
                'break_even' => $validated['break_even'] ?? null,
            ]
        );

        return redirect()->route('admin.nightly-reports.locations.index')
            ->with('success', "Location '{$loc->name}' created successfully.");
    }

    public function update(Request $request, $id)
    {
        $loc = NrLocation::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|string',
            'website_id' => 'nullable|exists:websites,id',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'gm_name' => 'nullable|string|max:100',
            'gm_email' => 'nullable|email|max:150',
            'nightly_goal' => 'nullable|numeric|min:0',
            'break_even' => 'nullable|numeric|min:0',
            'historical_best' => 'nullable|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);

        $loc->update($validated);

        NrBenchmark::updateOrCreate(
            ['location_id' => $loc->id],
            [
                'historical_best' => $validated['historical_best'] ?? null,
                'break_even' => $validated['break_even'] ?? null,
            ]
        );

        return redirect()->route('admin.nightly-reports.locations.index')
            ->with('success', "Location '{$loc->name}' updated successfully.");
    }

    public function toggleActive($id)
    {
        $loc = NrLocation::findOrFail($id);
        $loc->active = !$loc->active;
        $loc->save();

        return redirect()->route('admin.nightly-reports.locations.index')
            ->with('success', "Location '{$loc->name}' status changed to " . ($loc->active ? 'Active' : 'Inactive') . '.');
    }
}
