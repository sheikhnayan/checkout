<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLocation;
use Illuminate\Support\Facades\Auth;

class BaseNightlyReportsController extends Controller
{
    /**
     * Get accessible locations for the current authenticated user.
     */
    protected function accessibleLocations()
    {
        $user = Auth::guard('ambassador')->user() ?: Auth::user();
        if (!$user) {
            return NrLocation::whereRaw('1=0')->get();
        }

        if ($ambassador = Auth::guard('ambassador')->user()) {
            $ambassadorLocs = NrLocation::whereIn('website_id', $ambassador->clubs()->pluck('websites.id'))
                ->where('active', true)
                ->orderBy('name')
                ->get();
            if ($ambassadorLocs->isNotEmpty()) {
                return $ambassadorLocs;
            }
            return NrLocation::where('active', true)->orderBy('name')->get();
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return NrLocation::where('active', true)->orderBy('name')->get();
        }

        $locationIds = $user->accessibleNrLocationIds();
        if (!empty($locationIds)) {
            return NrLocation::whereIn('id', $locationIds)
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        return NrLocation::where('active', true)->orderBy('name')->get();
    }

    /**
     * Get accessible location IDs for the current authenticated user.
     */
    protected function accessibleLocationIds(): array
    {
        $ambassadorGuard = Auth::guard('ambassador');
        $ambassador = $ambassadorGuard->user();
        $user = $ambassador ?: Auth::user();
        if (!$user) {
            return [];
        }

        if ($ambassador) {
            $ambassadorLocIds = NrLocation::whereIn('website_id', $ambassador->clubs()->pluck('websites.id'))
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
            if (!empty($ambassadorLocIds)) {
                return $ambassadorLocIds;
            }
            return NrLocation::pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return NrLocation::pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        $locationIds = $user->accessibleNrLocationIds();
        if (!empty($locationIds)) {
            return $locationIds;
        }

        return NrLocation::pluck('id')->map(fn ($id) => (int) $id)->all();
    }

    /**
     * Apply location scope to any query builder instance.
     */
    protected function scopeLocation($query, $column = 'location_id', $selectedLocationId = null)
    {
        $allowedIds = $this->accessibleLocationIds();

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedIds, true)) {
            return $query->where($column, (int) $selectedLocationId);
        }

        return $query->whereIn($column, $allowedIds);
    }

    /**
     * Send email notifications for a newly created report to submitter, additional recipients, and GM.
     */
    public static function sendReportNotificationEmails($report)
    {
        try {
            if (!$report) {
                return;
            }

            $report->loadMissing('location');

            $recipients = [];

            // 1. Submitter Email
            if (!empty($report->submitter_email)) {
                $rawSubmitter = preg_split('/[,;\s]+/', $report->submitter_email);
                foreach ($rawSubmitter as $email) {
                    $clean = strtolower(trim($email));
                    if (!empty($clean) && filter_var($clean, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = $clean;
                    }
                }
            }

            // 2. Additional Recipient Email(s)
            if (!empty($report->additional_recipient)) {
                $rawAdditional = preg_split('/[,;\s]+/', $report->additional_recipient);
                foreach ($rawAdditional as $email) {
                    $clean = strtolower(trim($email));
                    if (!empty($clean) && filter_var($clean, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = $clean;
                    }
                }
            }

            // 3. Location GM Email
            if ($report->location && !empty($report->location->gm_email)) {
                $rawGm = preg_split('/[,;\s]+/', $report->location->gm_email);
                foreach ($rawGm as $email) {
                    $clean = strtolower(trim($email));
                    if (!empty($clean) && filter_var($clean, FILTER_VALIDATE_EMAIL)) {
                        $recipients[] = $clean;
                    }
                }
            }

            $recipients = array_unique(array_filter($recipients));

            if (empty($recipients)) {
                return;
            }

            $locationName = $report->location->name ?? 'Venue Operations';
            $businessDate = is_a($report->business_date ?? null, \Carbon\Carbon::class) 
                ? $report->business_date->format('M d, Y') 
                : (isset($report->business_date) ? \Carbon\Carbon::parse($report->business_date)->format('M d, Y') : date('M d, Y'));

            $subject = "Nightly Operations Report — {$locationName} ({$businessDate})";

            $html = view('emails.nightly-report-summary', compact('report'))->render();

            foreach ($recipients as $recipientEmail) {
                \Illuminate\Support\Facades\Mail::html($html, function ($message) use ($recipientEmail, $subject) {
                    $message->to($recipientEmail)
                        ->subject($subject)
                        ->from('no-reply@cartvip.com', 'CartVIP Nightly Reports');
                });
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send nightly report notification emails: ' . $e->getMessage());
        }
    }
}
