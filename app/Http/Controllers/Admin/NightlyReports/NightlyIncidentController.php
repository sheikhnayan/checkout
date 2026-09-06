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
            return $ambassador->clubs()->notArchived()->pluck('websites.id')->map(fn($id) => (int) $id)->toArray();
        }

        $user = Auth::user();
        if (!$user) {
            return [];
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return Website::notArchived()->pluck('id')->map(fn($id) => (int) $id)->toArray();
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

    public function create(Request $request)
    {
        $locations = $this->accessibleLocations();
        $websiteIds = $this->accessibleWebsiteIds();
        $websites = Website::whereIn('id', $websiteIds)->notArchived()->orderBy('name')->get();
        $selectedWebsiteId = $request->input('website_id');

        return view('admin.nightly-reports.incidents.create', compact('locations', 'websites', 'selectedWebsiteId'));
    }

    public function store(Request $request)
    {
        $websiteId = (int) $request->input('website_id');
        $websiteIds = $this->accessibleWebsiteIds();
        if (!in_array($websiteId, $websiteIds, true)) {
            abort(403, 'Access denied for this location.');
        }

        $validated = $request->validate([
            'website_id' => ['required', 'integer', 'exists:websites,id'],
            'location_legal_name' => ['required', 'string', 'max:255'],
            'location_dba_name' => ['required', 'string', 'max:255'],
            'location_address' => ['required', 'string', 'max:255'],
            'incident_calendar_date' => ['required', 'date'],
            'date_submitted' => ['required', 'date'],
            'incident_time' => ['required'],
            'incident_type' => ['nullable', 'string', 'max:255'],
            'police_report_number' => ['nullable', 'string', 'max:255'],
            'police_officers_badges' => ['nullable', 'string', 'max:5000'],
            'reporter_name' => ['required', 'string', 'max:255'],
            'managers_on_duty' => ['required', 'string', 'max:255'],
            'manager_phone' => ['nullable', 'string', 'max:100'],
            'involved_injured_persons' => ['required', 'string'],
            'incident_description' => ['required', 'string'],
            'witnesses_statement' => ['required', 'string'],
            'camera_angles' => ['required', 'string', 'max:5000'],
            'camera_timestamp' => ['required', 'string', 'max:255'],
            'cast_members_involved' => ['required', 'string', 'max:5000'],
            'additional_media_notes' => ['nullable', 'string'],
            'signature_choice' => ['required', 'in:accept,opt_out'],
            'digital_signature_name' => ['required', 'string', 'max:255'],
            'police_report_file' => ['nullable', 'file', 'max:4096'],
            'witness_report_files' => ['nullable', 'array', 'max:10'],
            'witness_report_files.*' => ['file', 'max:4096'],
            'additional_media_files' => ['nullable', 'array', 'max:5'],
            'additional_media_files.*' => ['file', 'max:4096'],
        ]);

        $incident = new Incident();
        $incident->fill($validated);
        $incident->website_id = $websiteId;
        $incident->status = 'open';
        $incident->status_changed_at = now();
        $incident->status_changed_by_user_id = auth()->id();
        $incident->public_witness_token = (string) \Illuminate\Support\Str::uuid();
        $incident->accepted_esignature = $request->input('signature_choice') === 'accept';
        $incident->opted_out_esignature = $request->input('signature_choice') === 'opt_out';
        $incident->created_by_user_id = auth()->id();
        $incident->save();

        if ($request->hasFile('police_report_file')) {
            $this->persistIncidentAttachment($incident, $request->file('police_report_file'), 'police_report');
        }
        foreach ((array) $request->file('witness_report_files', []) as $file) {
            $this->persistIncidentAttachment($incident, $file, 'witness_report');
        }
        foreach ((array) $request->file('additional_media_files', []) as $file) {
            $this->persistIncidentAttachment($incident, $file, 'additional_media');
        }

        IncidentAuditLog::create([
            'incident_id' => $incident->id,
            'user_id' => auth()->id(),
            'action' => 'incident_created',
            'change_summary' => [
                'status' => $incident->status,
                'reporter_name' => $incident->reporter_name,
                'incident_date' => optional($incident->incident_calendar_date)->format('Y-m-d'),
            ],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
        ]);

        return redirect()->route('admin.nightly-reports.incidents.show', $incident->id)
            ->with('success', 'Security incident report created successfully.');
    }

    private function persistIncidentAttachment(Incident $incident, $file, string $type): void
    {
        $dir = public_path('uploads/incidents/main');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = (int) $file->getSize();
        $fileName = 'incident_' . $incident->id . '_' . $type . '_' . time() . '_' . \Illuminate\Support\Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $fileName);

        \App\Models\IncidentAttachment::create([
            'incident_id' => $incident->id,
            'attachment_type' => $type,
            'file_path' => 'incidents/main/' . $fileName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);
    }
}
