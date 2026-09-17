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

        $websites = Website::orderBy('name')->get();

        return view('admin.nightly-reports.locations.index', compact('locations', 'websites'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:200',
            'legal_name' => 'nullable|string|max:200',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|string',
            'website_id' => 'nullable|exists:websites,id',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'gm_name' => 'nullable|string|max:100',
            'gm_email' => 'nullable|email|max:150',
            'club_inbox_email' => 'nullable|email|max:150',
            'nightly_goal' => 'nullable|numeric|min:0',
            'nightly_goals' => 'nullable|array',
            'nightly_goals.*' => 'nullable|numeric|min:0',
            'break_even' => 'nullable|numeric|min:0',
            'historical_best' => 'nullable|numeric|min:0',
        ]);

        if (isset($validated['nightly_goals']) && is_array($validated['nightly_goals'])) {
            $cleanGoals = [];
            foreach ($validated['nightly_goals'] as $day => $val) {
                if ($val !== '' && $val !== null) {
                    $cleanGoals[strtolower($day)] = (float) $val;
                }
            }
            $validated['nightly_goals'] = !empty($cleanGoals) ? $cleanGoals : null;
        }

        // Dynamically save only columns that exist in the database table to prevent 1054 Unknown column errors
        $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('nr_locations');
        $saveData = [];
        foreach ($validated as $k => $v) {
            if (in_array($k, $tableColumns)) {
                $saveData[$k] = $v;
            }
        }
        // Fallback for legal_name if column not migrated yet
        if (!in_array('legal_name', $tableColumns) && in_array('short_name', $tableColumns) && !empty($validated['legal_name'])) {
            $saveData['short_name'] = $validated['legal_name'];
        }
        // Fallback for nightly_goals if column not migrated yet
        if (!in_array('nightly_goals', $tableColumns) && in_array('operating_days', $tableColumns) && !empty($validated['nightly_goals'])) {
            $saveData['operating_days'] = $validated['nightly_goals'];
        }

        $loc = NrLocation::create($saveData);

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
            'legal_name' => 'nullable|string|max:200',
            'short_name' => 'nullable|string|max:100',
            'type' => 'required|string',
            'website_id' => 'nullable|exists:websites,id',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'gm_name' => 'nullable|string|max:100',
            'gm_email' => 'nullable|email|max:150',
            'club_inbox_email' => 'nullable|email|max:150',
            'nightly_goal' => 'nullable|numeric|min:0',
            'nightly_goals' => 'nullable|array',
            'nightly_goals.*' => 'nullable|numeric|min:0',
            'break_even' => 'nullable|numeric|min:0',
            'historical_best' => 'nullable|numeric|min:0',
            'active' => 'nullable|boolean',
        ]);

        if (isset($validated['nightly_goals']) && is_array($validated['nightly_goals'])) {
            $cleanGoals = [];
            foreach ($validated['nightly_goals'] as $day => $val) {
                if ($val !== '' && $val !== null) {
                    $cleanGoals[strtolower($day)] = (float) $val;
                }
            }
            $validated['nightly_goals'] = !empty($cleanGoals) ? $cleanGoals : null;
        }

        // Dynamically save only columns that exist in the database table to prevent 1054 Unknown column errors
        $tableColumns = \Illuminate\Support\Facades\Schema::getColumnListing('nr_locations');
        $saveData = [];
        foreach ($validated as $k => $v) {
            if (in_array($k, $tableColumns)) {
                $saveData[$k] = $v;
            }
        }
        // Fallback for legal_name if column not migrated yet
        if (!in_array('legal_name', $tableColumns) && in_array('short_name', $tableColumns) && array_key_exists('legal_name', $validated)) {
            $saveData['short_name'] = $validated['legal_name'];
        }
        // Fallback for nightly_goals if column not migrated yet
        if (!in_array('nightly_goals', $tableColumns) && in_array('operating_days', $tableColumns) && array_key_exists('nightly_goals', $validated)) {
            $saveData['operating_days'] = $validated['nightly_goals'];
        }

        $loc->update($saveData);

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
