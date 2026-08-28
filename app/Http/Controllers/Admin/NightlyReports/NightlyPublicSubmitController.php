<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;
use App\Models\NightlyReports\NrNightlyReport;
use App\Models\NightlyReports\NrBoutiqueReport;
use App\Models\NightlyReports\NrCohReport;
use App\Models\NightlyReports\NrIncident;
use App\Models\NightlyReports\NrWitnessStatement;
use App\Models\NightlyReports\NrHighTransaction;
use App\Models\NightlyReports\NrModelRelease;
use App\Models\NightlyReports\NrFormConfig;
use Carbon\Carbon;

class NightlyPublicSubmitController extends Controller
{
    /**
     * 1. Public Nightly Operations Report Intake Form
     */
    public function showNightly(Request $request)
    {
        $locations = NrLocation::where('active', true)->where('type', '!=', 'Boutique')->orderBy('name')->get();
        $selectedLocationId = $request->input('location');
        $defaultDate = Carbon::now()->hour < 6 ? Carbon::yesterday()->toDateString() : Carbon::today()->toDateString();
        $configs = NrFormConfig::where('report_type', 'nightly')->get()->keyBy('field_key');

        return view('admin.nightly-reports.public.submit-nightly', compact('locations', 'selectedLocationId', 'defaultDate', 'configs'));
    }

    public function storeNightly(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:nr_locations,id',
            'business_date' => 'required|date',
            'submitter_name' => 'required|string|max:150',
            'submitter_email' => 'required|email|max:150',
            'additional_contributor' => 'nullable|string|max:150',
            'additional_recipient' => 'nullable|email|max:150',
            'net_sales' => 'required|numeric|min:0',
            'nightly_goal' => 'nullable|numeric|min:0',
            'last_year_net_sales' => 'nullable|numeric|min:0',
            'weekly_running_net_sales' => 'nullable|numeric|min:0',
            'day_shift_net_sales' => 'nullable|numeric|min:0',
            'voids' => 'nullable|numeric|min:0',
            'comps' => 'nullable|numeric|min:0',
            'dance_dollars_sold' => 'nullable|numeric|min:0',
            'dance_dollars_redeemed' => 'nullable|numeric|min:0',
            'vip_rooms_sold' => 'nullable|integer|min:0',
            'total_guests' => 'required|integer|min:0',
            'paid_guests' => 'nullable|integer|min:0',
            'free_discount_guests' => 'nullable|integer|min:0',
            'passes_redeemed' => 'nullable|integer|min:0',
            'ipes' => 'nullable|integer|min:0',
            'taxi_payout' => 'nullable|numeric|min:0',
            'atm_payout' => 'nullable|numeric|min:0',
            'other_payouts' => 'nullable|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
            'safe_balance' => 'nullable|numeric|min:0',
            'weather' => 'nullable|string|max:100',
            'incident_flag' => 'required|boolean',
            'incident_notes' => 'nullable|string',
            'team_member_notes' => 'nullable|string',
            'ipe_notes' => 'nullable|string',
            'social_media_content' => 'nullable|string',
            'ordering_notes' => 'nullable|string',
            'nightly_checklists' => 'nullable|string',
            'pass_distribution_locations' => 'nullable|string',
            'night_summary' => 'nullable|string',
            'super_star_nomination' => 'nullable|string|max:200',
            'shift_comments' => 'nullable|string',
        ]);

        $validated['total_payouts'] = ($validated['taxi_payout'] ?? 0) + ($validated['atm_payout'] ?? 0) + ($validated['other_payouts'] ?? 0);
        $validated['guest_average'] = $validated['total_guests'] > 0 ? ($validated['net_sales'] / $validated['total_guests']) : 0;
        if (!empty($validated['ipes']) && $validated['ipes'] > 0 && !empty($validated['dance_dollars_sold'])) {
            $validated['dance_average'] = $validated['dance_dollars_sold'] / $validated['ipes'];
        }

        $report = NrNightlyReport::create($validated);

        return redirect()->route('nightly.submit.success', ['id' => $report->id, 'type' => 'nightly']);
    }

    /**
     * 2. Public Boutique Store Intake Form
     */
    public function showBoutique(Request $request)
    {
        $locations = NrLocation::where('active', true)->where('type', 'Boutique')->orderBy('name')->get();
        $selectedLocationId = $request->input('location');
        $defaultDate = Carbon::today()->toDateString();
        $configs = NrFormConfig::where('report_type', 'boutique')->get()->keyBy('field_key');

        return view('admin.nightly-reports.public.submit-boutique', compact('locations', 'selectedLocationId', 'defaultDate', 'configs'));
    }

    public function storeBoutique(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:nr_locations,id',
            'business_date' => 'required|date',
            'submitter_name' => 'required|string|max:150',
            'submitter_email' => 'required|email|max:150',
            'gross_daily_sales' => 'required|numeric|min:0',
            'daily_sales_goal' => 'nullable|numeric|min:0',
            'total_guest_count' => 'required|integer|min:0',
            'arcade_theater_guest_count' => 'nullable|integer|min:0',
            'current_week_total_sales' => 'nullable|numeric|min:0',
            'total_returns' => 'nullable|numeric|min:0',
            'total_discount' => 'nullable|numeric|min:0',
            'total_payouts' => 'nullable|numeric|min:0',
            'atm_payouts' => 'nullable|numeric|min:0',
            'gift_cards_sold' => 'nullable|numeric|min:0',
            'beginning_safe_balance' => 'nullable|numeric|min:0',
            'ending_safe_balance' => 'nullable|numeric|min:0',
            'said_deposit' => 'nullable|numeric|min:0',
            'actual_deposit' => 'nullable|numeric|min:0',
            'sales_direction' => 'required|in:UP,DOWN',
            'sales_direction_reason' => 'required|string',
            'incident_flag' => 'required|boolean',
            'daytime_shift_notes' => 'nullable|string',
            'nighttime_shift_notes' => 'nullable|string',
        ]);

        $validated['guest_average_ticket'] = $validated['total_guest_count'] > 0 ? ($validated['gross_daily_sales'] / $validated['total_guest_count']) : 0;

        $report = NrBoutiqueReport::create($validated);

        return redirect()->route('nightly.submit.success', ['id' => $report->id, 'type' => 'boutique']);
    }

    /**
     * 3. Public COH Form
     */
    public function showCOH(Request $request)
    {
        $locations = NrLocation::where('active', true)->orderBy('name')->get();
        $selectedLocationId = $request->input('location');
        $defaultDate = Carbon::today()->toDateString();
        $configs = NrFormConfig::where('report_type', 'coh')->get()->keyBy('field_key');

        return view('admin.nightly-reports.public.submit-coh', compact('locations', 'selectedLocationId', 'defaultDate', 'configs'));
    }

    public function storeCOH(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:nr_locations,id',
            'business_date' => 'required|date',
            'submitter_name' => 'required|string|max:150',
            'submitter_email' => 'required|email|max:150',
            'drop_safe' => 'nullable|numeric|min:0',
            'main_safe' => 'nullable|numeric|min:0',
            'register_1' => 'nullable|numeric|min:0',
            'register_2' => 'nullable|numeric|min:0',
            'register_3' => 'nullable|numeric|min:0',
            'register_4' => 'nullable|numeric|min:0',
            'atm_1' => 'nullable|numeric|min:0',
            'atm_2' => 'nullable|numeric|min:0',
            'atm_3' => 'nullable|numeric|min:0',
            'atm_4' => 'nullable|numeric|min:0',
            'other' => 'nullable|numeric|min:0',
            'paid_outs_total' => 'nullable|numeric|min:0',
            'paid_outs_explanation' => 'nullable|string',
            'e_signature' => 'nullable|string',
        ]);

        $safes = ($validated['drop_safe'] ?? 0) + ($validated['main_safe'] ?? 0);
        $registers = ($validated['register_1'] ?? 0) + ($validated['register_2'] ?? 0) + ($validated['register_3'] ?? 0) + ($validated['register_4'] ?? 0);
        $atms = ($validated['atm_1'] ?? 0) + ($validated['atm_2'] ?? 0) + ($validated['atm_3'] ?? 0) + ($validated['atm_4'] ?? 0);
        $other = $validated['other'] ?? 0;
        $paidOuts = $validated['paid_outs_total'] ?? 0;

        $validated['vu_cash_on_hand'] = ($safes + $registers + $atms + $other) - $paidOuts;

        $report = NrCohReport::create($validated);

        return redirect()->route('nightly.submit.success', ['id' => $report->id, 'type' => 'coh']);
    }

    /**
     * 4. Public Incident Form
     */
    public function showIncident(Request $request)
    {
        $locations = NrLocation::where('active', true)->orderBy('name')->get();
        $selectedLocationId = $request->input('location');
        $defaultDate = Carbon::today()->toDateString();
        $configs = NrFormConfig::where('report_type', 'incident')->get()->keyBy('field_key');

        return view('admin.nightly-reports.public.submit-incident', compact('locations', 'selectedLocationId', 'defaultDate', 'configs'));
    }

    public function storeIncident(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:nr_locations,id',
            'incident_date' => 'required|date',
            'time_of_incident' => 'required|string|max:50',
            'report_type_field' => 'required|string|max:100',
            'submitter_name' => 'required|string|max:150',
            'gm_email' => 'nullable|email|max:150',
            'managers_on_duty' => 'nullable|string|max:255',
            'manager_phone' => 'nullable|string|max:50',
            'cast_members_on_duty' => 'nullable|string',
            'involved_persons' => 'nullable|string',
            'incident_description' => 'required|string',
            'witnesses' => 'nullable|string',
            'police_report_number' => 'nullable|string|max:100',
            'police_officers_badges' => 'nullable|string|max:255',
            'camera_angles' => 'nullable|string',
            'camera_timestamp' => 'nullable|string|max:100',
            'restricted' => 'nullable|boolean',
            'e_signature' => 'nullable|string',
        ]);

        $location = NrLocation::find($validated['location_id']);

        $mainIncident = \App\Models\Incident::create([
            'website_id' => $location?->website_id,
            'location_legal_name' => $location?->name ?? 'Venue',
            'location_dba_name' => $location?->short_name ?? $location?->name ?? 'Venue',
            'location_address' => $location?->address ?? '',
            'incident_calendar_date' => $validated['incident_date'],
            'date_submitted' => now(),
            'incident_time' => $validated['time_of_incident'],
            'incident_type' => $validated['report_type_field'],
            'reporter_name' => $validated['submitter_name'],
            'managers_on_duty' => $validated['managers_on_duty'] ?? '',
            'manager_phone' => $validated['manager_phone'] ?? '',
            'involved_injured_persons' => $validated['involved_persons'] ?? '',
            'incident_description' => $validated['incident_description'],
            'witnesses_statement' => $validated['witnesses'] ?? '',
            'police_report_number' => $validated['police_report_number'] ?? null,
            'police_officers_badges' => $validated['police_officers_badges'] ?? null,
            'camera_angles' => $validated['camera_angles'] ?? null,
            'camera_timestamp' => $validated['camera_timestamp'] ?? null,
            'digital_signature_name' => $validated['e_signature'] ?? null,
            'status' => 'open',
            'public_witness_token' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        $incident = NrIncident::create($validated);

        return redirect()->route('nightly.submit.success', ['id' => $mainIncident->id, 'type' => 'incident']);
    }

    /**
     * 5. Public Witness Form (QR Door Intake)
     */
    public function showWitness(Request $request)
    {
        $locations = NrLocation::where('active', true)->orderBy('name')->get();
        $selectedLocationId = $request->input('location');
        $incidentId = $request->input('incident_id');
        $defaultDate = Carbon::today()->toDateString();
        $configs = NrFormConfig::where('report_type', 'witness')->get()->keyBy('field_key');

        return view('admin.nightly-reports.public.submit-witness', compact('locations', 'selectedLocationId', 'incidentId', 'defaultDate', 'configs'));
    }

    public function storeWitness(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:nr_locations,id',
            'incident_id' => 'nullable|exists:nr_incidents,id',
            'incident_date' => 'required|date',
            'witness_name' => 'required|string|max:150',
            'witness_address' => 'nullable|string|max:255',
            'witness_phone' => 'nullable|string|max:50',
            'witness_email' => 'nullable|email|max:150',
            'witness_type' => 'required|string|max:100',
            'statement_text' => 'required|string',
            'submitter_email' => 'nullable|email|max:150',
            'e_signature' => 'nullable|string',
        ]);

        $witness = NrWitnessStatement::create($validated);

        return redirect()->route('nightly.submit.success', ['id' => $witness->id, 'type' => 'witness']);
    }

    /**
     * Success Confirmation Screen
     */
    public function success(Request $request)
    {
        $type = $request->input('type', 'report');
        $id = $request->input('id');
        return view('admin.nightly-reports.public.success', compact('type', 'id'));
    }
}
