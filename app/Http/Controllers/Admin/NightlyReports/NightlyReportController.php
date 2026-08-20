<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrBoutiqueReport;
use App\Models\NightlyReports\NrCohReport;
use App\Models\NightlyReports\NrLocation;

class NightlyReportController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $reportType = $request->input('report_type', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $search = $request->input('search');

        $query = NrNightlyReport::with('location')
            ->whereIn('location_id', $allowedLocationIds);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('business_date', [$startDate, $endDate]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_email', 'like', "%{$search}%")
                    ->orWhere('night_summary', 'like', "%{$search}%")
                    ->orWhere('team_member_notes', 'like', "%{$search}%");
            });
        }

        $reports = $query->orderByDesc('business_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('admin.nightly-reports.reports.index', compact(
            'reports',
            'locations',
            'selectedLocationId',
            'reportType',
            'startDate',
            'endDate',
            'search'
        ));
    }

    public function show($type, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();

        if ($type === 'boutique') {
            $report = NrBoutiqueReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            $report->update(['is_viewed' => true]);
            return view('admin.nightly-reports.reports.show-boutique', compact('report'));
        }

        if ($type === 'coh') {
            $report = NrCohReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            return view('admin.nightly-reports.reports.show-coh', compact('report'));
        }

        $report = NrNightlyReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
        $report->update(['is_viewed' => true]);

        return view('admin.nightly-reports.reports.show', compact('report'));
    }

    public function edit($type, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $locations = $this->accessibleLocations();

        if ($type === 'boutique') {
            $report = NrBoutiqueReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            return view('admin.nightly-reports.reports.edit-boutique', compact('report', 'locations'));
        }

        if ($type === 'coh') {
            $report = NrCohReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            return view('admin.nightly-reports.reports.edit-coh', compact('report', 'locations'));
        }

        $report = NrNightlyReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);
        return view('admin.nightly-reports.reports.edit', compact('report', 'locations'));
    }

    public function update(Request $request, $type, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();

        if ($type === 'boutique') {
            $report = NrBoutiqueReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            $validated = $request->validate([
                'gross_daily_sales' => 'required|numeric|min:0',
                'total_guest_count' => 'required|integer|min:0',
                'sales_direction' => 'nullable|string',
                'sales_direction_reason' => 'nullable|string',
                'daytime_shift_notes' => 'nullable|string',
                'nighttime_shift_notes' => 'nullable|string',
            ]);
            $report->update($validated);
            return redirect()->route('admin.nightly-reports.reports.show', ['type' => 'boutique', 'id' => $report->id])
                ->with('success', 'Boutique report updated successfully.');
        }

        if ($type === 'coh') {
            $report = NrCohReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            $validated = $request->validate([
                'drop_safe' => 'nullable|numeric|min:0',
                'main_safe' => 'nullable|numeric|min:0',
                'paid_outs_total' => 'nullable|numeric|min:0',
                'paid_outs_explanation' => 'nullable|string',
            ]);
            $report->update($validated);
            return redirect()->route('admin.nightly-reports.reports.show', ['type' => 'coh', 'id' => $report->id])
                ->with('success', 'COH report updated successfully.');
        }

        $report = NrNightlyReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
        $validated = $request->validate([
            'net_sales' => 'required|numeric|min:0',
            'nightly_goal' => 'nullable|numeric|min:0',
            'total_guests' => 'required|integer|min:0',
            'paid_guests' => 'nullable|integer|min:0',
            'free_discount_guests' => 'nullable|integer|min:0',
            'passes_redeemed' => 'nullable|integer|min:0',
            'taxi_payout' => 'nullable|numeric|min:0',
            'atm_payout' => 'nullable|numeric|min:0',
            'other_payouts' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'safe_balance' => 'nullable|numeric|min:0',
            'weather' => 'nullable|string|max:100',
            'team_member_notes' => 'nullable|string',
            'ipe_notes' => 'nullable|string',
            'social_media_content' => 'nullable|string',
            'ordering_notes' => 'nullable|string',
            'night_summary' => 'nullable|string',
            'super_star_nomination' => 'nullable|string|max:200',
            'shift_comments' => 'nullable|string',
        ]);

        $validated['total_payouts'] = ($validated['taxi_payout'] ?? 0) + ($validated['atm_payout'] ?? 0) + ($validated['other_payouts'] ?? 0);
        $validated['guest_average'] = $validated['total_guests'] > 0 ? ($validated['net_sales'] / $validated['total_guests']) : 0;

        $report->update($validated);

        return redirect()->route('admin.nightly-reports.reports.show', ['type' => 'nightly', 'id' => $report->id])
            ->with('success', 'Nightly report updated successfully.');
    }

    public function destroy($type, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();

        if ($type === 'boutique') {
            $report = NrBoutiqueReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            $report->delete();
            return redirect()->route('admin.nightly-reports.boutique.index')
                ->with('success', 'Boutique report deleted successfully.');
        }

        if ($type === 'coh') {
            $report = NrCohReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
            $report->delete();
            return redirect()->route('admin.nightly-reports.coh.index')
                ->with('success', 'COH report deleted successfully.');
        }

        $report = NrNightlyReport::whereIn('location_id', $allowedLocationIds)->findOrFail($id);
        $report->delete();

        return redirect()->route('admin.nightly-reports.reports.index')
            ->with('success', 'Nightly report deleted successfully.');
    }

    public function previewEmail($type, $id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $report = NrNightlyReport::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);

        return view('admin.nightly-reports.reports.email-preview', compact('report'));
    }
}
