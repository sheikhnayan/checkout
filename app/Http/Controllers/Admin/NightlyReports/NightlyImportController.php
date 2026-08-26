<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;

class NightlyImportController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        return view('admin.nightly-reports.imports.index', compact('locations'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        
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
            $exists = \App\Models\NightlyReports\NrNightlyReport::where('location_id', $location->id)
                ->where('business_date', $businessDate)
                ->exists();

            if ($exists) {
                $skippedCount++;
                continue;
            }

            // Map and format numbers
            $cleanNum = function($val) {
                if (empty($val)) return 0;
                $val = str_replace([',', '$'], '', $val);
                return is_numeric($val) ? (float) $val : 0;
            };

            $incidentFlag = false;
            $incidentText = $data['Where there any incidents, altercations, accidents?'] ?? '';
            $incidentTextLower = strtolower(trim($incidentText));
            if (!empty($incidentTextLower) && !in_array($incidentTextLower, ['none', 'no', 'na', 'n/a', 'false', '0'])) {
                $incidentFlag = true;
            }

            \App\Models\NightlyReports\NrNightlyReport::create([
                'location_id' => $location->id,
                'business_date' => $businessDate,
                
                // Submitter Info
                'submitter_name' => trim(($data['Your Name (First)'] ?? '') . ' ' . ($data['Your Name (Last)'] ?? '')),
                'submitter_email' => $data['Email'] ?? 'imported@example.com',
                'additional_contributor' => trim(($data['Additional Contributor (if applicable) (First)'] ?? '') . ' ' . ($data['Additional Contributor (if applicable) (Last)'] ?? '')),
                'additional_recipient' => $data['Additional Recipient'] ?? null,
                
                // Financials
                'nightly_goal' => $cleanNum($data['Nightly Goal'] ?? 0),
                'net_sales' => $cleanNum($data['Net Sales'] ?? 0),
                'last_year_net_sales' => $cleanNum($data['Last Year Net Sales'] ?? 0),
                'weekly_running_net_sales' => $cleanNum($data['Weekly Running Net Sales'] ?? 0),
                'day_shift_net_sales' => $cleanNum($data['Day Shift Net Sales'] ?? 0),
                'voids' => $cleanNum($data['Voids'] ?? 0),
                'comps' => $cleanNum($data['Comps'] ?? 0),
                'dance_dollars_sold' => $cleanNum($data['Dance Dollars Sold'] ?? 0),
                'dance_dollars_redeemed' => $cleanNum($data['Dance Dollars Redeemed'] ?? 0),
                
                // Attendance
                'vip_rooms_sold' => (int) $cleanNum($data['# VIP/ Champagne Rooms Sold'] ?? 0),
                'total_guests' => (int) $cleanNum($data['Total Guests'] ?? 0),
                'paid_guests' => (int) $cleanNum($data['.... Paid'] ?? 0),
                'free_discount_guests' => (int) $cleanNum($data['.... Free / Discount'] ?? 0),
                'passes_redeemed' => (int) $cleanNum($data['Passes Redeemed'] ?? 0),
                'ipes' => (int) $cleanNum($data['# IPE\'S'] ?? 0),
                
                // Payouts & Vault
                'total_payouts' => $cleanNum($data['Total Payouts'] ?? 0),
                'taxi_payout' => $cleanNum($data['Taxi Payout'] ?? 0),
                'atm_payout' => $cleanNum($data['.... ATM Payout'] ?? 0),
                'other_payouts' => $cleanNum($data['.... Other Payouts'] ?? 0),
                'deposit' => $cleanNum($data['Deposit'] ?? 0),
                'safe_balance' => $cleanNum($data['Safe Balance'] ?? 0),
                
                // Operations
                'weather' => $data['Weather '] ?? null, // Mind the trailing space in header
                'incident_flag' => $incidentFlag,
                'incident_notes' => $incidentFlag ? $incidentText : null,
                
                // Notes
                'team_member_notes' => $data['Team member notes'] ?? null,
                'ipe_notes' => $data['IPE Notes'] ?? null,
                'social_media_content' => $data['Social Media Content - Did you send any content to the social media department tonight? If so, what? If Not, why?'] ?? null,
                'ordering_notes' => $data['Ordering Notes'] ?? null,
                'nightly_checklists' => $data['Nightly Checklists / Forms'] ?? null,
                'pass_distribution_locations' => $data['What locations did you distribute your 300 passes today?'] ?? null,
                'night_summary' => $data['Did you beat the goal? Why or why not? What were your successes/challenges? Sum up the night in a few words including any successes or challenges from promotions, incidents, etc.'] ?? null,
                'super_star_nomination' => $data['Did your location nominate a SUPER STAR recently? If not, why?'] ?? null,
                'shift_comments' => $data['Shift Comments -Include other relevant comments about tonight\'s shift (overall thoughts, rush times, Best Factor, etc).'] ?? null,
                
                // Extracted system metrics
                'browser' => $data['Browser'] ?? null,
                'ip_address' => $data['IP Address'] ?? null,
                'unique_id' => $data['Unique ID'] ?? null,
                'submission_location' => $data['Location'] ?? null,
            ]);

            $importedCount++;
        }

        fclose($handle);

        return redirect()->route('admin.nightly-reports.reports.index')->with('success', "Import complete: {$importedCount} records imported, {$skippedCount} duplicates skipped.");
    }
}
