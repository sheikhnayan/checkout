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
        $ambassador = Auth::guard('ambassador')->user();
        $user = $ambassador ?: Auth::user();
        if (!$user) {
            return NrLocation::whereRaw('1=0')->get();
        }

        if ($ambassador) {
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

        $locationIds = $this->accessibleLocationIds();
        if (!empty($locationIds)) {
            return NrLocation::whereIn('id', $locationIds)
                ->where('active', true)
                ->orderBy('name')
                ->get();
        }

        return NrLocation::whereRaw('1=0')->get();
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

        $directLocationIds = $user->accessibleNrLocationIds();

        // Include locations of ambassadors created/managed by this user
        $ambassadorWebsiteIds = \Illuminate\Support\Facades\DB::table('ambassador_website')
            ->join('nightly_report_ambassadors', 'ambassador_website.nightly_report_ambassador_id', '=', 'nightly_report_ambassadors.id')
            ->where('nightly_report_ambassadors.created_by_user_id', $user->id)
            ->pluck('ambassador_website.website_id')
            ->toArray();

        $ambassadorLocationIds = [];
        if (!empty($ambassadorWebsiteIds)) {
            $ambassadorLocationIds = NrLocation::whereIn('website_id', $ambassadorWebsiteIds)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all();
        }

        return array_values(array_unique(array_merge($directLocationIds, $ambassadorLocationIds)));
    }

    /**
     * Get email addresses of ambassadors created/managed by the current user.
     */
    protected function managedAmbassadorEmails(): array
    {
        $ambassador = Auth::guard('ambassador')->user();
        $user = $ambassador ?: Auth::user();
        if (!$user || $user->isAdmin() || $user->isSuperAdmin()) {
            return [];
        }

        return \App\Models\NightlyReportAmbassador::where('created_by_user_id', $user->id)
            ->pluck('email')
            ->map(fn($e) => strtolower(trim($e)))
            ->filter()
            ->all();
    }

    /**
     * Scope a report query to only show reports the user is allowed to see:
     * - Admins/SuperAdmins: All reports
     * - Managers/Users: Reports for their accessible locations OR submitted by them OR submitted by ambassadors under them.
     */
    protected function scopeReportQuery($query)
    {
        $user = Auth::guard('ambassador')->user() ?: Auth::user();
        if (!$user) {
            return $query->whereRaw('1=0');
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            return $query;
        }

        $allowedLocationIds = $this->accessibleLocationIds();
        $userEmail = strtolower(trim($user->email ?? ''));
        $ambassadorEmails = $this->managedAmbassadorEmails();

        return $query->where(function ($q) use ($allowedLocationIds, $userEmail, $ambassadorEmails) {
            $hasCondition = false;
            if (!empty($allowedLocationIds)) {
                $q->whereIn('location_id', $allowedLocationIds);
                $hasCondition = true;
            }
            if ($userEmail) {
                if ($hasCondition) {
                    $q->orWhereRaw('LOWER(submitter_email) = ?', [$userEmail]);
                } else {
                    $q->whereRaw('LOWER(submitter_email) = ?', [$userEmail]);
                    $hasCondition = true;
                }
            }
            if (!empty($ambassadorEmails)) {
                foreach ($ambassadorEmails as $aEmail) {
                    if ($hasCondition) {
                        $q->orWhereRaw('LOWER(submitter_email) = ?', [$aEmail]);
                    } else {
                        $q->whereRaw('LOWER(submitter_email) = ?', [$aEmail]);
                        $hasCondition = true;
                    }
                }
            }

            if (!$hasCondition) {
                $q->whereRaw('1=0');
            }
        });
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
