<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\IncidentAuditLog;
use App\Models\NightlyReports\NrLocation;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class NightlyIncidentController extends BaseNightlyReportsController
{
    protected function accessibleWebsiteIds(): array
    {
        if ($ambassador = Auth::guard('ambassador')->user()) {
            return $ambassador->clubs()->pluck('websites.id')->map(fn($id) => (int) $id)->toArray();
        }

        $user = Auth::user();
        if (!$user) {
            return [];
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return Website::pluck('id')->map(fn($id) => (int) $id)->toArray();
        }

        return array_map('intval', $user->accessibleWebsiteIds());
    }

    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $websiteIds = $this->accessibleWebsiteIds();

        $selectedLocationId = $request->input('location_id');
        $status = $request->input('status');
        $search = $request->input('search');

        $filterWebsiteId = null;
        if ($selectedLocationId) {
            $loc = NrLocation::find($selectedLocationId);
            $filterWebsiteId = $loc?->website_id ?? $selectedLocationId;
        }

        $query = Incident::with(['website', 'witnessReports', 'attachments'])
            ->whereIn('website_id', $websiteIds);

        if ($filterWebsiteId && in_array((int) $filterWebsiteId, $websiteIds, true)) {
            $query->where('website_id', (int) $filterWebsiteId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reporter_name', 'like', "%{$search}%")
                    ->orWhere('incident_type', 'like', "%{$search}%")
                    ->orWhere('incident_description', 'like', "%{$search}%")
                    ->orWhere('police_report_number', 'like', "%{$search}%")
                    ->orWhere('involved_injured_persons', 'like', "%{$search}%")
                    ->orWhere('location_legal_name', 'like', "%{$search}%");
            });
        }

        $incidents = $query->orderByDesc('incident_calendar_date')
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
        $websiteIds = $this->accessibleWebsiteIds();
        $incident = Incident::with([
            'website',
            'creator',
            'statusChangedBy',
            'attachments',
            'witnessReports.attachments',
            'auditLogs.user',
        ])
            ->whereIn('website_id', $websiteIds)
            ->findOrFail($id);

        return view('admin.nightly-reports.incidents.show', compact('incident'));
    }

    public function updateStatus(Request $request, $id)
    {
        $websiteIds = $this->accessibleWebsiteIds();
        $incident = Incident::whereIn('website_id', $websiteIds)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|string|in:open,under_review,legal_hold,closed,resolved',
            'status_note' => 'nullable|string|max:2000',
        ]);

        $oldStatus = (string) $incident->status;
        $newStatus = (string) $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Incident status is already set to ' . str_replace('_', ' ', $newStatus) . '.');
        }

        $incident->status = $newStatus;
        $incident->status_changed_at = now();
        $incident->status_changed_by_user_id = auth()->id();
        $incident->save();

        IncidentAuditLog::create([
            'incident_id' => $incident->id,
            'user_id' => auth()->id(),
            'action' => 'incident_status_updated',
            'change_summary' => [
                'from' => $oldStatus,
                'to' => $newStatus,
                'note' => $validated['status_note'] ?? null,
            ],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
        ]);

        return back()->with('success', 'Incident status updated successfully.');
    }
}
