<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Incident;
use App\Models\IncidentAttachment;
use App\Models\IncidentAuditLog;
use App\Models\Website;
use App\Models\WitnessReport;
use App\Models\WitnessReportAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    private const INCIDENT_TIMEZONE = 'America/Los_Angeles';

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $websites = Website::where('is_archieved', 0)
                ->orderBy('name')
                ->get()
                ->map(function (Website $website) {
                    $website->incident_count = Incident::where('website_id', $website->id)->count();
                    return $website;
                });
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            $websites = Website::where('id', $user->website_id)
                ->where('is_archieved', 0)
                ->get()
                ->map(function (Website $website) {
                    $website->incident_count = Incident::where('website_id', $website->id)->count();
                    return $website;
                });
        } else {
            $websites = collect();
        }

        return view('admin.incident.index', compact('websites'));
    }

    public function show(string $websiteId)
    {
        $website = Website::findOrFail($websiteId);
        $this->ensureWebsiteAccess((int) $websiteId);

        $incidents = Incident::with(['creator', 'witnessReports'])
            ->where('website_id', $websiteId)
            ->latest()
            ->get();

        return view('admin.incident.show', [
            'website' => $website,
            'websiteId' => (int) $websiteId,
            'incidents' => $incidents,
        ]);
    }

    public function create(string $websiteId)
    {
        $website = Website::findOrFail($websiteId);
        $this->ensureWebsiteAccess((int) $websiteId);

        return view('admin.incident.create', [
            'website' => $website,
            'websiteId' => (int) $websiteId,
        ]);
    }

    public function store(Request $request)
    {
        $websiteId = (int) $request->input('website_id');
        $this->ensureWebsiteAccess($websiteId);

        $validated = $request->validate($this->incidentValidationRules());

        $incident = new Incident();
        $incident->fill($validated);
        $incident->website_id = $websiteId;
        $incident->status = 'open';
        $incident->status_changed_at = now();
        $incident->status_changed_by_user_id = auth()->id();
        $incident->public_witness_token = (string) Str::uuid();
        $incident->accepted_esignature = $request->input('signature_choice') === 'accept';
        $incident->opted_out_esignature = $request->input('signature_choice') === 'opt_out';
        $incident->created_by_user_id = auth()->id();
        $incident->save();

        $this->storeIncidentAttachments($request, $incident);
        $this->appendAuditLog($incident, $request, 'incident_created', [
            'status' => $incident->status,
            'reporter_name' => $incident->reporter_name,
            'incident_date' => optional($incident->incident_calendar_date)->format('Y-m-d'),
        ]);

        return redirect()->route('admin.incident.details', $incident->id)
            ->with('success', 'Incident report created successfully.');
    }

    public function details(string $incidentId)
    {
        $incident = Incident::with([
            'website',
            'creator',
            'statusChangedBy',
            'attachments',
            'witnessReports.attachments',
            'auditLogs.user',
        ])->findOrFail($incidentId);

        $this->ensureWebsiteAccess((int) $incident->website_id);

        return view('admin.incident.details', compact('incident'));
    }

    public function createWitness(string $incidentId)
    {
        $incident = Incident::with('website')->findOrFail($incidentId);
        $this->ensureWebsiteAccess((int) $incident->website_id);

        return view('admin.incident.witness-create', [
            'incident' => $incident,
            'source' => 'admin_panel',
        ]);
    }

    public function storeWitness(Request $request, string $incidentId)
    {
        $incident = Incident::with('website')->findOrFail($incidentId);
        $this->ensureWebsiteAccess((int) $incident->website_id);

        $validated = $request->validate($this->witnessValidationRules());

        $witness = new WitnessReport();
        $witness->fill($validated);
        $witness->incident_id = $incident->id;
        $witness->submitted_by_user_id = auth()->id();
        $witness->submitted_via = 'admin_panel';
        $witness->accepted_esignature = $request->input('signature_choice') === 'accept';
        $witness->opted_out_esignature = $request->input('signature_choice') === 'opt_out';
        $witness->save();

        $this->storeWitnessAttachment($request, $witness);
        $this->appendAuditLog($incident, $request, 'witness_added_from_admin', [
            'witness_report_id' => $witness->id,
            'witness_name' => $witness->full_name,
        ]);

        return redirect()->route('admin.incident.details', $incident->id)
            ->with('success', 'Witness report added successfully.');
    }

    public function publicWitnessForm(string $token)
    {
        $incident = Incident::with('website')->where('public_witness_token', $token)->firstOrFail();

        return view('incident.witness-public', [
            'incident' => $incident,
            'source' => 'shared_link',
        ]);
    }

    public function publicWitnessStore(Request $request, string $token)
    {
        $incident = Incident::where('public_witness_token', $token)->firstOrFail();
        $validated = $request->validate($this->witnessValidationRules());

        $witness = new WitnessReport();
        $witness->fill($validated);
        $witness->incident_id = $incident->id;
        $witness->submitted_by_user_id = auth()->id();
        $witness->submitted_via = 'shared_link';
        $witness->accepted_esignature = $request->input('signature_choice') === 'accept';
        $witness->opted_out_esignature = $request->input('signature_choice') === 'opt_out';
        $witness->save();

        $this->storeWitnessAttachment($request, $witness);
        $this->appendAuditLog($incident, $request, 'witness_added_from_shared_link', [
            'witness_report_id' => $witness->id,
            'witness_name' => $witness->full_name,
        ], false);

        return redirect()->route('incident.witness.form', $token)
            ->with('success', 'Witness report submitted successfully.');
    }

    public function updateStatus(Request $request, string $incidentId)
    {
        $incident = Incident::findOrFail($incidentId);
        $this->ensureWebsiteAccess((int) $incident->website_id);

        $validated = $request->validate([
            'status' => ['required', 'in:open,under_review,closed'],
            'status_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $oldStatus = (string) $incident->status;
        $newStatus = (string) $validated['status'];

        if ($oldStatus === $newStatus) {
            return redirect()->route('admin.incident.details', $incident->id)
                ->with('info', 'Incident status is already set to ' . str_replace('_', ' ', $newStatus) . '.');
        }

        $incident->status = $newStatus;
        $incident->status_changed_at = now();
        $incident->status_changed_by_user_id = auth()->id();
        $incident->save();

        $this->appendAuditLog($incident, $request, 'incident_status_updated', [
            'from' => $oldStatus,
            'to' => $newStatus,
            'note' => $validated['status_note'] ?? null,
        ]);

        return redirect()->route('admin.incident.details', $incident->id)
            ->with('success', 'Incident status updated successfully.');
    }

    public function export(string $incidentId)
    {
        $incident = Incident::with([
            'website',
            'creator',
            'statusChangedBy',
            'attachments',
            'witnessReports.attachments',
            'auditLogs.user',
        ])->findOrFail($incidentId);

        $this->ensureWebsiteAccess((int) $incident->website_id);

        $this->appendAuditLog($incident, $request = request(), 'incident_exported_pdf', [
            'format' => 'pdf',
        ]);

        $fileName = 'incident-report-' . $incident->id . '-' . now(self::INCIDENT_TIMEZONE)->format('Ymd_His') . '.pdf';
        $pdf = Pdf::loadView('admin.incident.export-pdf', compact('incident'))->setPaper('a4');

        return $pdf->download($fileName);
    }

    public function printWitness(string $witnessId)
    {
        $witness = WitnessReport::with(['incident.website', 'attachments'])->findOrFail($witnessId);
        $incident = $witness->incident;

        $this->ensureWebsiteAccess((int) $incident->website_id);

        $this->appendAuditLog($incident, request(), 'witness_report_printed', [
            'witness_report_id' => $witness->id,
            'witness_name' => $witness->full_name,
        ]);

        return view('admin.incident.witness-print', compact('witness', 'incident'));
    }

    public function downloadWitness(string $witnessId)
    {
        $witness = WitnessReport::with(['incident.website', 'attachments'])->findOrFail($witnessId);
        $incident = $witness->incident;

        $this->ensureWebsiteAccess((int) $incident->website_id);

        $this->appendAuditLog($incident, request(), 'witness_report_downloaded', [
            'witness_report_id' => $witness->id,
            'witness_name' => $witness->full_name,
        ]);

        $pdf = app('dompdf.wrapper');
        $html = view('admin.incident.witness-print', compact('witness', 'incident'))->render();
        $pdf->loadHTML($html);

        return $pdf->download('witness_statement_' . $witness->id . '_' . now(self::INCIDENT_TIMEZONE)->format('Y-m-d_His') . '.pdf');
    }

    private function incidentValidationRules(): array
    {
        return [
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
        ];
    }

    private function witnessValidationRules(): array
    {
        return [
            'location_legal_name' => ['required', 'string', 'max:255'],
            'location_dba_name' => ['required', 'string', 'max:255'],
            'location_address' => ['required', 'string', 'max:255'],
            'incident_calendar_date' => ['required', 'date'],
            'date_submitted' => ['required', 'date'],
            'incident_time' => ['required'],
            'incident_type' => ['required', 'string', 'max:255'],
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:100'],
            'participant_type' => ['required', 'in:Witness,Involved Person'],
            'detailed_statement' => ['required', 'string'],
            'signature_choice' => ['required', 'in:accept,opt_out'],
            'digital_signature_name' => ['required', 'string', 'max:255'],
            'evidence_file' => ['nullable', 'file', 'max:4096'],
        ];
    }

    private function storeIncidentAttachments(Request $request, Incident $incident): void
    {
        if ($request->hasFile('police_report_file')) {
            $this->persistIncidentAttachment($incident, $request->file('police_report_file'), 'police_report');
        }

        foreach ((array) $request->file('witness_report_files', []) as $file) {
            $this->persistIncidentAttachment($incident, $file, 'witness_report');
        }

        foreach ((array) $request->file('additional_media_files', []) as $file) {
            $this->persistIncidentAttachment($incident, $file, 'additional_media');
        }
    }

    private function storeWitnessAttachment(Request $request, WitnessReport $witness): void
    {
        if (!$request->hasFile('evidence_file')) {
            return;
        }

        $file = $request->file('evidence_file');
        $this->ensureUploadDirectory(public_path('uploads/incidents/witness'));
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = (int) $file->getSize();
        $fileName = 'witness_' . $witness->id . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/incidents/witness'), $fileName);

        WitnessReportAttachment::create([
            'witness_report_id' => $witness->id,
            'attachment_type' => 'evidence',
            'file_path' => 'incidents/witness/' . $fileName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);
    }

    private function persistIncidentAttachment(Incident $incident, $file, string $type): void
    {
        $this->ensureUploadDirectory(public_path('uploads/incidents/main'));
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getClientMimeType();
        $fileSize = (int) $file->getSize();
        $fileName = 'incident_' . $incident->id . '_' . $type . '_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/incidents/main'), $fileName);

        IncidentAttachment::create([
            'incident_id' => $incident->id,
            'attachment_type' => $type,
            'file_path' => 'incidents/main/' . $fileName,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isWebsiteUser() && (int) $user->website_id === $websiteId) {
            return;
        }

        abort(403, 'Access denied for this website.');
    }

    private function ensureUploadDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        mkdir($path, 0775, true);
    }

    private function appendAuditLog(Incident $incident, Request $request, string $action, array $summary = [], bool $useAuthUser = true): void
    {
        IncidentAuditLog::create([
            'incident_id' => $incident->id,
            'user_id' => $useAuthUser ? auth()->id() : null,
            'action' => $action,
            'change_summary' => $summary ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
        ]);
    }
}
