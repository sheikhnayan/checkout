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
            'additional_recipient' => 'nullable|email|max:150',
            'incident_notes' => 'nullable|string',
            'team_member_notes' => 'nullable|string',
            'ipe_notes' => 'nullable|string',
            'social_media_content' => 'nullable|string',
            'ordering_notes' => 'nullable|string',
            'nightly_checklists' => 'nullable|string',
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

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file'
        ]);

        $file = $request->file('csv_file');
        
        ini_set('auto_detect_line_endings', true);
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        if (!$header) {
            return redirect()->back()->with('error', 'Failed to read CSV header.');
        }

        // Ensure BOM is removed from first column header if present
        $header[0] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header[0]);
        // Trim headers
        $header = array_map('trim', $header);
        
        $importedCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty rows
            if (empty(array_filter($row))) continue;

            // Handle column count mismatch
            if (count($header) > count($row)) {
                $row = array_pad($row, count($header), '');
            } elseif (count($row) > count($header)) {
                $row = array_slice($row, 0, count($header));
            }

            $data = array_combine($header, $row);
            if (!$data) continue;

            if (empty($data['Business Date'])) continue;

            $businessDate = date('Y-m-d', strtotime($data['Business Date']));

            // Find or create location
            $locationName = $data['Location Name'] ?? 'Unknown Location';
            $locationCity = $data['City'] ?? null;
            $locationType = $data['Location Type'] ?? 'Adult with Liquor';
            $location = NrLocation::firstOrCreate(
                ['name' => $locationName],
                [
                    'city' => $locationCity,
                    'type' => $locationType
                ]
            );

            // Skip duplicates
            $exists = NrNightlyReport::where('location_id', $location->id)
                ->where('business_date', $businessDate)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // Split names
            $firstName = $data['Your Name (First)'] ?? '';
            $lastName = $data['Your Name (Last)'] ?? '';
            $submitterName = trim($firstName . ' ' . $lastName);

            $addFirstName = $data['Additional Contributor (if applicable) (First)'] ?? '';
            $addLastName = $data['Additional Contributor (if applicable) (Last)'] ?? '';
            $additionalContributor = trim($addFirstName . ' ' . $addLastName);
            if ($additionalContributor === '') $additionalContributor = null;

            // Handle incident flag
            $incidentText = $data['Where there any incidents, altercations, accidents?'] ?? 'None';
            $incidentFlag = false;
            $incidentNotes = null;
            if (strtolower(trim($incidentText)) !== 'none' && strtolower(trim($incidentText)) !== 'no' && strtolower(trim($incidentText)) !== 'na' && strtolower(trim($incidentText)) !== 'false') {
                $incidentFlag = true;
                $incidentNotes = $incidentText;
            }

            // Create report
            NrNightlyReport::create([
                'location_id' => $location->id,
                'business_date' => $businessDate,
                'submitter_name' => $submitterName,
                'submitter_email' => $data['Email'] ?? '',
                'additional_contributor' => $additionalContributor,
                'additional_recipient' => $data['Additional Recipient'] ?? null,
                'net_sales' => (float)str_replace(',', '', $data['Net Sales'] ?? 0),
                'nightly_goal' => (float)str_replace(',', '', $data['Nightly Goal'] ?? 0),
                'last_year_net_sales' => (float)str_replace(',', '', $data['Last Year Net Sales'] ?? 0),
                'weekly_running_net_sales' => (float)str_replace(',', '', $data['Weekly Running Net Sales'] ?? 0),
                'day_shift_net_sales' => (float)str_replace(',', '', $data['Day Shift Net Sales'] ?? 0),
                'voids' => (float)str_replace(',', '', $data['Voids'] ?? 0),
                'comps' => (float)str_replace(',', '', $data['Comps'] ?? 0),
                'dance_dollars_sold' => (float)str_replace(',', '', $data['Dance Dollars Sold'] ?? 0),
                'dance_dollars_redeemed' => (float)str_replace(',', '', $data['Dance Dollars Redeemed'] ?? 0),
                'vip_rooms_sold' => (int)str_replace(',', '', $data['# VIP/ Champagne Rooms Sold'] ?? 0),
                'total_guests' => (int)str_replace(',', '', $data['Total Guests'] ?? 0),
                'paid_guests' => (int)str_replace(',', '', $data['.... Paid'] ?? 0),
                'free_discount_guests' => (int)str_replace(',', '', $data['.... Free / Discount'] ?? 0),
                'passes_redeemed' => (int)str_replace(',', '', $data['Passes Redeemed'] ?? 0),
                'guest_average' => (float)str_replace(',', '', $data['Guest Average'] ?? 0),
                'dance_average' => (float)str_replace(',', '', $data['Dance Average'] ?? 0),
                'ipes' => (int)str_replace(',', '', $data['# IPE\'S'] ?? 0),
                'total_payouts' => (float)str_replace(',', '', $data['Total Payouts'] ?? 0),
                'taxi_payout' => (float)str_replace(',', '', $data['Taxi Payout'] ?? 0),
                'atm_payout' => (float)str_replace(',', '', $data['.... ATM Payout'] ?? 0),
                'other_payouts' => (float)str_replace(',', '', $data['.... Other Payouts'] ?? 0),
                'deposit' => (float)str_replace(',', '', $data['Deposit'] ?? 0),
                'safe_balance' => (float)str_replace(',', '', $data['Safe Balance'] ?? 0),
                'weather' => $data['Weather '] ?? null,
                'incident_flag' => $incidentFlag,
                'incident_notes' => $incidentNotes,
                'team_member_notes' => $data['Team member notes'] ?? null,
                'ipe_notes' => $data['IPE Notes'] ?? null,
                'social_media_content' => $data['Social Media Content - Did you send any content to the social media department tonight? If so, what? If Not, why?'] ?? null,
                'ordering_notes' => $data['Ordering Notes'] ?? null,
                'nightly_checklists' => $data['Nightly Checklists / Forms'] ?? null,
                'pass_distribution_locations' => $data['What locations did you distribute your 300 passes today?'] ?? null,
                'night_summary' => $data['Did you beat the goal? Why or why not? What were your successes/challenges? Sum up the night in a few words including any successes or challenges from promotions, incidents, etc.'] ?? null,
                'super_star_nomination' => $data['Did your location nominate a SUPER STAR recently? If not, why?'] ?? null,
                'shift_comments' => $data['Shift Comments -Include other relevant comments about tonight\'s shift (overall thoughts, rush times, Best Factor, etc).'] ?? null,
                'browser' => $data['Browser'] ?? null,
                'ip_address' => $data['IP Address'] ?? null,
                'unique_id' => $data['Unique ID'] ?? null,
                'submission_location' => $data['Location'] ?? null,
                'created_at' => date('Y-m-d H:i:s', strtotime($data['Time'] ?? 'now')),
                'source' => 'csv_import'
            ]);

            $importedCount++;
        }

        fclose($handle);

        return redirect()->back()->with('success', "Import complete: $importedCount records imported, $skippedCount duplicates skipped.");
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'nightly_reports_export_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Time', 'Business Date', 'Location Type', 'Location Name', 'City', 
            'Your Name (First)', 'Your Name (Last)', 
            'Additional Contributor (if applicable) (First)', 'Additional Contributor (if applicable) (Last)', 
            'Email', 'Nightly Goal', 'Net Sales', 'Last Year Net Sales', 'Weekly Running Net Sales', 'Day Shift Net Sales', 
            'Voids', 'Comps', 'Dance Dollars Sold', 'Dance Dollars Redeemed', '# VIP/ Champagne Rooms Sold', 
            'Total Guests', '.... Paid', '.... Free / Discount', 'Passes Redeemed', 'Guest Average', 'Dance Average', 
            '# IPE\'S', 'Total Payouts', 'Taxi Payout', '.... ATM Payout', '.... Other Payouts', 'Deposit', 'Safe Balance', 
            'Weather ', 'Where there any incidents, altercations, accidents?', 'Team member notes', 'IPE Notes', 
            'Social Media Content - Did you send any content to the social media department tonight? If so, what? If Not, why?', 
            'Ordering Notes', 'Nightly Checklists / Forms', 'What locations did you distribute your 300 passes today?', 
            'Did you beat the goal? Why or why not? What were your successes/challenges? Sum up the night in a few words including any successes or challenges from promotions, incidents, etc.', 
            'Did your location nominate a SUPER STAR recently? If not, why?', 
            'Shift Comments -Include other relevant comments about tonight\'s shift (overall thoughts, rush times, Best Factor, etc).', 
            'Additional Recipient', 'Browser', 'IP Address', 'Unique ID', 'Location'
        ];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $reports = NrNightlyReport::with('location')->orderByDesc('business_date')->get();

            foreach ($reports as $report) {
                // Split names
                $submitterParts = explode(' ', $report->submitter_name, 2);
                $submitterFirst = $submitterParts[0] ?? '';
                $submitterLast = $submitterParts[1] ?? '';

                $addParts = explode(' ', $report->additional_contributor, 2);
                $addFirst = $addParts[0] ?? '';
                $addLast = $addParts[1] ?? '';

                $incidentText = $report->incident_flag ? ($report->incident_notes ?? 'Yes') : 'None';

                $row = [
                    $report->created_at->format('D M d H:i:s \U\T\C Y'), // Format like the CSV
                    $report->business_date ? $report->business_date->format('Y-m-d') : '',
                    $report->location ? $report->location->type : '',
                    $report->location ? $report->location->name : '',
                    $report->location ? $report->location->city : '',
                    $submitterFirst,
                    $submitterLast,
                    $addFirst,
                    $addLast,
                    $report->submitter_email,
                    $report->nightly_goal,
                    $report->net_sales,
                    $report->last_year_net_sales,
                    $report->weekly_running_net_sales,
                    $report->day_shift_net_sales,
                    $report->voids,
                    $report->comps,
                    $report->dance_dollars_sold,
                    $report->dance_dollars_redeemed,
                    $report->vip_rooms_sold,
                    $report->total_guests,
                    $report->paid_guests,
                    $report->free_discount_guests,
                    $report->passes_redeemed,
                    $report->guest_average,
                    $report->dance_average,
                    $report->ipes,
                    $report->total_payouts,
                    $report->taxi_payout,
                    $report->atm_payout,
                    $report->other_payouts,
                    $report->deposit,
                    $report->safe_balance,
                    $report->weather,
                    $incidentText,
                    $report->team_member_notes,
                    $report->ipe_notes,
                    $report->social_media_content,
                    $report->ordering_notes,
                    $report->nightly_checklists,
                    $report->pass_distribution_locations,
                    $report->night_summary,
                    $report->super_star_nomination,
                    $report->shift_comments,
                    $report->additional_recipient,
                    $report->browser,
                    $report->ip_address,
                    $report->unique_id,
                    $report->submission_location
                ];

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
