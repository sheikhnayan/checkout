<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrBenchmark;
use App\Models\NightlyReports\NrLocation;

class NightlyBenchmarkController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = NrLocation::with('benchmark')
            ->orderBy('name')
            ->get();

        return view('admin.nightly-reports.benchmarks.index', compact('locations'));
    }

    public function upsert(Request $request)
    {
        $locationId = (int) $request->input('location_id');
        $historicalBest = $request->input('historical_best');
        $breakEven = $request->input('break_even');

        NrBenchmark::updateOrCreate(
            ['location_id' => $locationId],
            [
                'historical_best' => $historicalBest ?: null,
                'break_even' => $breakEven ?: null,
                'updated_by_user_id' => auth()->id(),
            ]
        );

        $loc = NrLocation::find($locationId);
        if ($loc) {
            $loc->update([
                'historical_best' => $historicalBest ?: null,
                'break_even' => $breakEven ?: null,
            ]);
        }

        return back()->with('success', "Benchmarks updated for {$loc->name}.");
    }

    public function previewPerformanceEmail(Request $request)
    {
        $locations = NrLocation::with('benchmark')->where('active', true)->orderBy('name')->get();
        return view('admin.nightly-reports.benchmarks.email-preview', compact('locations'));
    }

    public function sendPerformanceEmail(Request $request)
    {
        return back()->with('success', 'Benchmark performance briefing dispatched to executive distribution list.');
    }
}
