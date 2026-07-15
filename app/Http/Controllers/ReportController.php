<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UserReportPreference;
use App\Models\ReportExport;
use App\Models\AutomationReportRun;
use App\Models\AutomationReportSchedule;
use App\Models\Transaction;
use App\Models\Website;
use App\Models\WebsiteVisitorSession;
use App\Services\AutomationReportSchedulerService;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    private AutomationReportSchedulerService $automationScheduler;

    public function __construct(AutomationReportSchedulerService $automationScheduler)
    {
        $this->automationScheduler = $automationScheduler;
    }

    // Middleware handled in routes/web.php

    /**
     * Display list of reports accessible to the user
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $category = $request->get('category');

        $reports = Report::accessibleBy($user)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderBy('display_order')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $reports,
                'categories' => Report::distinct('category')->pluck('category'),
            ]);
        }

        return view('admin.reports.index', [
            'reports' => $reports,
            'selectedCategory' => $category,
            'categories' => Report::where('is_active', true)->distinct('category')->pluck('category'),
        ]);
    }

    /**
     * Show a specific report with data
     */
    public function show(Report $report, Request $request)
    {
        $user = auth()->user();

        // Check authorization
        if (!$report->canAccessBy($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get filters from request
        $filters = $request->only(array_keys($report->available_filters ?? []));

        // Handle custom date range
        if ($request->get('date_range') === 'custom') {
            $filters['date_range'] = 'custom';
            $filters['start_date'] = $request->get('custom_from');
            $filters['end_date'] = $request->get('custom_to');
        } else {
            $filters['date_range'] = $request->get('date_range', 'last_30_days');
        }

        // Generate report data
        $service = new ReportGenerationService($user, $filters);
        $data = $service->generate($report);

        // Get user's saved preferences for this report
        $preference = UserReportPreference::where('user_id', $user->id)
            ->where('report_id', $report->id)
            ->first();

        // Get all saved reports for this specific report
        $savedReports = UserReportPreference::where('user_id', $user->id)
            ->where('report_id', $report->id)
            ->orderByDesc('last_run_at')
            ->get();

        if ($request->wantsJson() || $request->boolean('ajax')) {
            return response()->json([
                'success' => true,
                'report' => $report,
                'data' => $data,
                'preference' => $preference,
                'savedReports' => $savedReports,
            ]);
        }

        return view('admin.reports.show', [
            'report' => $report,
            'data' => $data,
            'preference' => $preference,
            'savedReports' => $savedReports,
        ]);
    }

    /**
     * Save user report preferences
     */
    public function savePreference(Report $report, Request $request)
    {
        $user = auth()->user();

        if (!$report->canAccessBy($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'filters' => 'required|array',
            'columns' => 'nullable|array',
            'is_favorite' => 'boolean',
        ]);

        $preference = UserReportPreference::updateOrCreate(
            [
                'user_id' => $user->id,
                'report_id' => $report->id,
                'name' => $validated['name'],
            ],
            [
                'filters' => $validated['filters'],
                'columns' => $validated['columns'],
                'is_favorite' => $validated['is_favorite'] ?? false,
                'last_run_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Report preference saved successfully',
            'preference' => $preference,
        ]);
    }

    /**
     * Get user's saved report preferences
     */
    public function getSavedReports(Request $request)
    {
        $user = auth()->user();

        $preferences = UserReportPreference::where('user_id', $user->id)
            ->with('report')
            ->orderByDesc('is_favorite')
            ->orderByDesc('last_run_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Export report to CSV, PDF, or Excel
     */
    public function export(Report $report, Request $request)
    {
        $user = auth()->user();

        if (!$report->canAccessBy($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'filters' => 'nullable|array',
        ]);

        $filters = $validated['filters'] ?? [];

        // Generate report data
        $service = new ReportGenerationService($user, $filters);
        $data = $service->generate($report);

        // Create export record
        $export = ReportExport::create([
            'user_id' => $user->id,
            'report_id' => $report->id,
            'format' => $validated['format'],
            'filters' => $filters,
            'status' => 'pending',
        ]);

        // For now, return data - in production would queue a job
        return match ($validated['format']) {
            'csv' => $this->exportToCsv($report, $data, $export),
            'excel' => $this->exportToExcel($report, $data, $export),
            'pdf' => $this->exportToPdf($report, $data, $export),
            default => response()->json(['error' => 'Invalid format'], 400),
        };
    }

    /**
     * Preview report PDF in browser (no export record creation)
     */
    public function previewPdf(Report $report, Request $request)
    {
        $user = auth()->user();

        if (!$report->canAccessBy($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $filters = $request->except(['_token']);

        if ($request->get('date_range') === 'custom') {
            $filters['date_range'] = 'custom';
            $filters['start_date'] = $request->get('custom_from', $request->get('start_date'));
            $filters['end_date'] = $request->get('custom_to', $request->get('end_date'));
        } else {
            $filters['date_range'] = $request->get('date_range', 'last_30_days');
        }

        $service = new ReportGenerationService($user, $filters);
        $data = $service->generate($report);

        $filename = $report->slug . '_preview_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'report' => $report,
            'data' => $data,
            'filters' => $filters,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream($filename);
    }

    /**
     * Export report as CSV
     */
    private function exportToCsv(Report $report, array $data, ReportExport $export)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$report->slug}_{$timestamp}.csv";

        $response = new StreamedResponse(function () use ($data) {
            $output = fopen('php://output', 'w');

            // Write headers
            if (!empty($data['data']) && is_array($data['data'])) {
                $firstRow = $data['data'][0];
                if (is_array($firstRow)) {
                    fputcsv($output, array_keys($firstRow));

                    foreach ($data['data'] as $row) {
                        fputcsv($output, $row);
                    }
                }
            }

            fclose($output);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$filename\"");

        // Update export record
        $export->update(['status' => 'completed', 'exported_at' => now()]);

        return $response;
    }

    /**
     * Export report as Excel
     */
    private function exportToExcel(Report $report, array $data, ReportExport $export)
    {
        // This would use a library like maatwebsite/excel
        // For now, return CSV
        return $this->exportToCsv($report, $data, $export);
    }

    /**
     * Export report as PDF
     */
    private function exportToPdf(Report $report, array $data, ReportExport $export)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "{$report->slug}_{$timestamp}.pdf";

        $filters = (array) ($export->filters ?? []);

        $pdf = Pdf::loadView('admin.reports.pdf', [
            'report' => $report,
            'data' => $data,
            'filters' => $filters,
            'generatedAt' => now(),
        ])->setPaper('a4', 'landscape');

        // Update export record
        $export->update(['status' => 'completed', 'exported_at' => now()]);

        return $pdf->download($filename);
    }

    /**
     * Generate an expiring signed URL for automation report preview.
     */
    public function automationTempUrl(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $params = $request->only(['period', 'from', 'to', 'website_ids', 'timezone', 'interactive']);

        $expiresAt = now()->addHours(12);
        $signedUrl = URL::temporarySignedRoute('admin.reports.automation.preview.signed', $expiresAt, $params);

        return response()->json([
            'success' => true,
            'url' => $signedUrl,
            'expires_at' => $expiresAt->toDateTimeString(),
        ]);
    }

    /**
     * Signed automation preview endpoint for fast approval cycles.
     */
    public function automationPreviewSigned(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Preview link expired or invalid.');
        }

        return $this->automationPreview($request);
    }

    /**
     * Automation-style executive PDF preview (independent of report catalog).
     */
    public function automationPreview(Request $request)
    {
        $timezone = $this->resolveAutomationTimezone($request);
        [$startAt, $endAt, $periodLabel] = $this->resolveAutomationDateRange($request, $timezone);
        $websiteIds = $this->resolveAutomationWebsiteIds($request);

        $txQuery = Transaction::query()
            ->with(['website', 'package'])
            ->financiallyReportable()
            ->whereBetween('created_at', [$startAt, $endAt]);

        if (!empty($websiteIds)) {
            $txQuery->whereIn('website_id', $websiteIds);
        }

        $transactions = $txQuery->get();

        $selectedWebsites = Website::query()
            ->when(!empty($websiteIds), fn ($q) => $q->whereIn('id', $websiteIds))
            ->orderBy('name')
            ->get(['id', 'name']);

        $sessionSnapshot = [
            'top_referrers' => collect(),
            'top_utm_sources' => collect(),
            'top_landing_pages' => collect(),
        ];
        $totalSessions = 0;
        $uniqueVisitors = 0;
        $averagePagesPerSession = 0.0;
        $averageSessionDurationSeconds = 0.0;
        $bouncedSessions = 0;
        $bounceRate = 0.0;

        if (Schema::hasTable('website_visitor_sessions')) {
            $sessionRangeStart = $startAt->copy()->utc();
            $sessionRangeEnd = $endAt->copy()->utc();

            $sessionBaseQuery = WebsiteVisitorSession::query()
                ->whereBetween('first_seen_at', [$sessionRangeStart, $sessionRangeEnd]);

            if (!empty($websiteIds)) {
                $sessionBaseQuery->whereIn('website_id', $websiteIds);
            }

            $sessionRows = (clone $sessionBaseQuery)->get([
                'visitor_key',
                'page_views',
                'first_seen_at',
                'last_seen_at',
            ]);

            $totalSessions = (int) $sessionRows->count();
            $uniqueVisitors = (int) $sessionRows->pluck('visitor_key')->filter()->unique()->count();
            $averagePagesPerSession = $totalSessions > 0
                ? (float) $sessionRows->avg(fn ($session) => max(1, (int) ($session->page_views ?? 1)))
                : 0.0;

            $averageSessionDurationSeconds = $totalSessions > 0
                ? (float) $sessionRows->avg(function ($session) {
                    if (empty($session->first_seen_at) || empty($session->last_seen_at)) {
                        return 0;
                    }
                    return max(0, $session->last_seen_at->diffInSeconds($session->first_seen_at));
                })
                : 0.0;

            $bouncedSessions = (int) $sessionRows->filter(fn ($session) => max(1, (int) ($session->page_views ?? 1)) <= 1)->count();
            $bounceRate = $totalSessions > 0 ? (($bouncedSessions / $totalSessions) * 100) : 0.0;

            $sessionSnapshot['top_referrers'] = (clone $sessionBaseQuery)
                ->select('referrer_host', DB::raw('COUNT(*) as sessions'))
                ->whereNotNull('referrer_host')
                ->where('referrer_host', '!=', '')
                ->groupBy('referrer_host')
                ->orderByDesc('sessions')
                ->limit(10)
                ->get();

            $sessionSnapshot['top_utm_sources'] = (clone $sessionBaseQuery)
                ->select('utm_source', DB::raw('COUNT(*) as sessions'))
                ->whereNotNull('utm_source')
                ->where('utm_source', '!=', '')
                ->groupBy('utm_source')
                ->orderByDesc('sessions')
                ->limit(10)
                ->get();

            $sessionSnapshot['top_landing_pages'] = (clone $sessionBaseQuery)
                ->select('landing_path', DB::raw('COUNT(*) as sessions'))
                ->whereNotNull('landing_path')
                ->where('landing_path', '!=', '')
                ->groupBy('landing_path')
                ->orderByDesc('sessions')
                ->limit(12)
                ->get();
        }

        $totalRevenue = (float) $transactions->sum('total');
        $totalTransactions = (int) $transactions->count();
        $uniquePatrons = (int) $transactions->pluck('package_email')->filter()->unique()->count();
        $totalGuests = (int) $transactions->sum(fn ($t) => max(1, (int) ($t->package_number_of_guest ?? 1)));
        $avgOrder = $totalTransactions > 0 ? ($totalRevenue / $totalTransactions) : 0;
        $affiliateCommission = (float) $transactions->sum('affiliate_commission_amount');
        $entertainerCommission = (float) $transactions->sum('entertainer_commission_amount');
        $totalCommission = $affiliateCommission + $entertainerCommission;

        $totalAddonsQty = 0;
        $transactionsWithAddons = 0;
        $addonBuckets = [];
        $packageAddonComboBuckets = [];
        $transportTransactions = 0;
        $transportRevenue = 0.0;
        $packageOnlyTransactions = 0;
        $packageOnlyRevenue = 0.0;
        foreach ($transactions as $transaction) {
            $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : [];
            $transactionAddonNames = [];
            $transactionHasAddons = false;
            $transactionHasTransportAddon = false;

            foreach ($cartItems as $item) {
                if (!is_array($item) || !isset($item['addons']) || !is_array($item['addons'])) {
                    continue;
                }

                $itemAddonNames = [];
                $packageLabel = trim((string) ($item['package_name'] ?? ''));
                if ($packageLabel === '') {
                    $packageLabel = trim((string) ($transaction->package_table_label ?? ''));
                }
                if ($packageLabel === '' || $packageLabel === 'N/A') {
                    $packageLabel = trim((string) (optional($transaction->package)->name ?? ''));
                }
                if ($packageLabel === '') {
                    $packageLabel = 'Package';
                }

                foreach ($item['addons'] as $addon) {
                    if (!is_array($addon)) {
                        continue;
                    }

                    $addonName = trim((string) ($addon['name'] ?? $addon['addon_name'] ?? $addon['title'] ?? 'Addon'));
                    if ($addonName === '') {
                        $addonName = 'Addon';
                    }

                    $qty = max(1, (int) ($addon['qty'] ?? $addon['quantity'] ?? 1));
                    $unitPrice = (float) ($addon['price'] ?? $addon['addon_price'] ?? $addon['amount'] ?? 0);
                    $lineRevenue = $qty * $unitPrice;

                    $totalAddonsQty += $qty;
                    $transactionHasAddons = true;

                    $addonKey = strtolower($addonName);
                    if (!isset($addonBuckets[$addonKey])) {
                        $addonBuckets[$addonKey] = [
                            'addon_name' => $addonName,
                            'qty' => 0,
                            'transactions' => 0,
                            'revenue' => 0.0,
                            'club_counts' => [],
                        ];
                    }
                    $addonBuckets[$addonKey]['qty'] += $qty;
                    $addonBuckets[$addonKey]['revenue'] += $lineRevenue;

                    $clubName = trim((string) (optional($transaction->website)->name ?? ''));
                    if ($clubName === '') {
                        $clubName = 'Unknown Club';
                    }
                    $addonBuckets[$addonKey]['club_counts'][$clubName] =
                        (int) ($addonBuckets[$addonKey]['club_counts'][$clubName] ?? 0) + $qty;

                    $itemAddonNames[$addonKey] = $addonName;
                    $transactionAddonNames[$addonKey] = $addonName;

                    if (
                        str_contains($addonKey, 'transport') ||
                        str_contains($addonKey, 'pickup') ||
                        str_contains($addonKey, 'driver') ||
                        str_contains($addonKey, 'limo') ||
                        str_contains($addonKey, 'shuttle')
                    ) {
                        $transactionHasTransportAddon = true;
                    }
                }

                if (!empty($itemAddonNames)) {
                    ksort($itemAddonNames);
                    $addonComboLabel = implode(' + ', array_values($itemAddonNames));
                    $packageComboKey = strtolower($packageLabel . ' || ' . $addonComboLabel);

                    if (!isset($packageAddonComboBuckets[$packageComboKey])) {
                        $packageAddonComboBuckets[$packageComboKey] = [
                            'package_name' => $packageLabel,
                            'addon_combo' => $addonComboLabel,
                            'label' => $packageLabel . ' + ' . $addonComboLabel,
                            'transactions' => 0,
                            'revenue' => 0.0,
                            'club_counts' => [],
                        ];
                    }
                    $packageAddonComboBuckets[$packageComboKey]['transactions']++;
                    $packageAddonComboBuckets[$packageComboKey]['revenue'] += (float) ($transaction->total ?? 0);

                    $comboClubName = trim((string) (optional($transaction->website)->name ?? ''));
                    if ($comboClubName === '') {
                        $comboClubName = 'Unknown Club';
                    }
                    $packageAddonComboBuckets[$packageComboKey]['club_counts'][$comboClubName] =
                        (int) ($packageAddonComboBuckets[$packageComboKey]['club_counts'][$comboClubName] ?? 0) + 1;
                }
            }

            if ($transactionHasAddons) {
                $transactionsWithAddons++;

                foreach (array_keys($transactionAddonNames) as $addonKey) {
                    if (isset($addonBuckets[$addonKey])) {
                        $addonBuckets[$addonKey]['transactions']++;
                    }
                }

                // Keep package+addon combinations as the main combination signal.
            } else {
                $packageOnlyTransactions++;
                $packageOnlyRevenue += (float) ($transaction->total ?? 0);
            }

            $hasTransportFields = trim((string) ($transaction->transportation_pickup_time ?? '')) !== ''
                || trim((string) ($transaction->transportation_address ?? '')) !== ''
                || trim((string) ($transaction->transportation_phone ?? '')) !== ''
                || trim((string) ($transaction->transportation_guest ?? '')) !== ''
                || trim((string) ($transaction->transportation_note ?? '')) !== '';

            if ($hasTransportFields || $transactionHasTransportAddon) {
                $transportTransactions++;
                $transportRevenue += (float) ($transaction->total ?? 0);
            }
        }

        $topAddons = collect($addonBuckets)
            ->sortByDesc('qty')
            ->take(15)
            ->map(function ($row) {
                $clubNames = collect($row['club_counts'] ?? [])
                    ->sortDesc()
                    ->keys()
                    ->take(2)
                    ->values()
                    ->implode(', ');
                $row['club_names'] = $clubNames !== '' ? $clubNames : 'Unknown Club';
                unset($row['club_counts']);
                return $row;
            })
            ->values();

        $topPackageAddonCombinations = collect($packageAddonComboBuckets)
            ->sortByDesc('transactions')
            ->take(14)
            ->map(function ($row) {
                $clubNames = collect($row['club_counts'] ?? [])
                    ->sortDesc()
                    ->keys()
                    ->take(2)
                    ->values()
                    ->implode(', ');
                $row['club_names'] = $clubNames !== '' ? $clubNames : 'Unknown Club';
                unset($row['club_counts']);
                return $row;
            })
            ->values();

        $selfDriveTransactions = max(0, $totalTransactions - $transportTransactions);
        $selfDriveRevenue = max(0, $totalRevenue - $transportRevenue);

        $transportSnapshot = [
            'transport' => [
                'transactions' => $transportTransactions,
                'revenue' => $transportRevenue,
            ],
            'self_drive' => [
                'transactions' => $selfDriveTransactions,
                'revenue' => $selfDriveRevenue,
            ],
        ];

        $packageModeSnapshot = [
            'package_only' => [
                'transactions' => $packageOnlyTransactions,
                'revenue' => $packageOnlyRevenue,
            ],
            'package_with_addons' => [
                'transactions' => $transactionsWithAddons,
                'revenue' => max(0, $totalRevenue - $packageOnlyRevenue),
            ],
        ];

        $clubSnapshot = $transactions
            ->groupBy('website_id')
            ->map(function ($items, $websiteId) {
                $first = $items->first();
                $revenue = (float) $items->sum('total');
                $txnCount = (int) $items->count();
                $unique = (int) $items->pluck('package_email')->filter()->unique()->count();
                $guests = (int) $items->sum(fn ($t) => max(1, (int) ($t->package_number_of_guest ?? 1)));
                return [
                    'website_id' => (int) $websiteId,
                    'website_name' => optional($first->website)->name ?: ('Website #' . $websiteId),
                    'transactions' => $txnCount,
                    'revenue' => $revenue,
                    'unique_patrons' => $unique,
                    'guests' => $guests,
                    'avg_order_value' => $txnCount > 0 ? ($revenue / $txnCount) : 0,
                ];
            })
            ->values()
            ->sortByDesc('revenue')
            ->values();

        $topPackages = $transactions
            ->groupBy(function ($t) {
                $label = trim((string) ($t->package_table_label ?? ''));
                if ($label !== '' && $label !== 'N/A') {
                    return $label;
                }
                $name = trim((string) (optional($t->package)->name ?? ''));
                if ($name !== '') {
                    return $name;
                }
                return 'Package';
            })
            ->map(function ($items, $label) {
                $clubNames = $items
                    ->groupBy(fn ($t) => trim((string) (optional($t->website)->name ?? '')) ?: 'Unknown Club')
                    ->map(fn ($clubItems) => $clubItems->count())
                    ->sortDesc()
                    ->keys()
                    ->take(2)
                    ->values()
                    ->implode(', ');

                return [
                    'package_name' => (string) $label,
                    'transactions' => (int) $items->count(),
                    'revenue' => (float) $items->sum('total'),
                    'guests' => (int) $items->sum(fn ($t) => max(1, (int) ($t->package_number_of_guest ?? 1))),
                    'club_names' => $clubNames !== '' ? $clubNames : 'Unknown Club',
                ];
            })
            ->sortByDesc('revenue')
            ->take(12)
            ->values();

        $dailyBuckets = [];
        $dailyCursor = $startAt->copy()->startOfDay();
        $dailyEnd = $endAt->copy()->startOfDay();
        while ($dailyCursor->lte($dailyEnd)) {
            $dayKey = $dailyCursor->format('Y-m-d');
            $dailyBuckets[$dayKey] = [
                'day' => $dayKey,
                'transactions' => 0,
                'revenue' => 0.0,
                'guests' => 0,
                'patron_emails' => [],
            ];
            $dailyCursor->addDay();
        }

        foreach ($transactions as $transaction) {
            if (!$transaction->created_at) {
                continue;
            }
            $dayKey = $transaction->created_at->copy()->timezone($timezone)->format('Y-m-d');
            if (!isset($dailyBuckets[$dayKey])) {
                continue;
            }
            $dailyBuckets[$dayKey]['transactions']++;
            $dailyBuckets[$dayKey]['revenue'] += (float) ($transaction->total ?? 0);
            $dailyBuckets[$dayKey]['guests'] += max(1, (int) ($transaction->package_number_of_guest ?? 1));
            $email = strtolower(trim((string) ($transaction->package_email ?? '')));
            if ($email !== '') {
                $dailyBuckets[$dayKey]['patron_emails'][$email] = true;
            }
        }

        $dailyTrend = collect($dailyBuckets)
            ->map(function ($row) {
                $row['unique_patrons'] = count($row['patron_emails'] ?? []);
                unset($row['patron_emails']);
                return $row;
            })
            ->values();

        $hourlyBuckets = collect(range(0, 23))->mapWithKeys(function ($hour) {
            $displayHour = $hour % 12;
            if ($displayHour === 0) {
                $displayHour = 12;
            }
            $suffix = $hour < 12 ? 'AM' : 'PM';

            return [$hour => [
                'hour' => $hour,
                'label' => sprintf('%d %s', $displayHour, $suffix),
                'transactions' => 0,
                'revenue' => 0.0,
                'guests' => 0,
            ]];
        })->all();

        $weekdayBuckets = [
            0 => ['weekday' => 'Sun', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            1 => ['weekday' => 'Mon', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            2 => ['weekday' => 'Tue', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            3 => ['weekday' => 'Wed', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            4 => ['weekday' => 'Thu', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            5 => ['weekday' => 'Fri', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
            6 => ['weekday' => 'Sat', 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0],
        ];

        $cityBuckets = [];
        $stateBuckets = [];
        $countryBuckets = [];

        $orderValueBands = [
            ['label' => '$0', 'exact' => 0.0, 'transactions' => 0, 'revenue' => 0.0],
            ['label' => '$1-$99', 'min' => 0.01, 'max' => 99.99, 'transactions' => 0, 'revenue' => 0.0],
            ['label' => '$100-$249', 'min' => 100, 'max' => 249.99, 'transactions' => 0, 'revenue' => 0.0],
            ['label' => '$250-$499', 'min' => 250, 'max' => 499.99, 'transactions' => 0, 'revenue' => 0.0],
            ['label' => '$500-$999', 'min' => 500, 'max' => 999.99, 'transactions' => 0, 'revenue' => 0.0],
            ['label' => '$1,000+', 'min' => 1000, 'max' => null, 'transactions' => 0, 'revenue' => 0.0],
        ];

        $leadTimeBands = [
            ['label' => 'Same Day', 'min' => 0, 'max' => 0, 'transactions' => 0],
            ['label' => '1 Day', 'min' => 1, 'max' => 1, 'transactions' => 0],
            ['label' => '2-3 Days', 'min' => 2, 'max' => 3, 'transactions' => 0],
            ['label' => '4-7 Days', 'min' => 4, 'max' => 7, 'transactions' => 0],
            ['label' => '8-14 Days', 'min' => 8, 'max' => 14, 'transactions' => 0],
            ['label' => '15+ Days', 'min' => 15, 'max' => null, 'transactions' => 0],
            ['label' => 'Past Use Date', 'special' => 'past_use', 'transactions' => 0],
            ['label' => 'No Use Date', 'special' => 'no_use_date', 'transactions' => 0],
        ];

        $leadTimeDays = [];
        $checkinLagMinutes = [];

        foreach ($transactions as $transaction) {
            if (!$transaction->created_at) {
                continue;
            }

            $createdAtLocal = $transaction->created_at->copy()->timezone($timezone);
            $hour = (int) $createdAtLocal->format('G');
            $dow = (int) $createdAtLocal->dayOfWeek;
            $amount = (float) ($transaction->total ?? 0);
            $guests = max(1, (int) ($transaction->package_number_of_guest ?? 1));

            $hourlyBuckets[$hour]['transactions']++;
            $hourlyBuckets[$hour]['revenue'] += $amount;
            $hourlyBuckets[$hour]['guests'] += $guests;

            $weekdayBuckets[$dow]['transactions']++;
            $weekdayBuckets[$dow]['revenue'] += $amount;
            $weekdayBuckets[$dow]['guests'] += $guests;

            $city = trim((string) ($transaction->payment_city ?? ''));
            if ($city !== '') {
                $cityKey = strtolower($city);
                if (!isset($cityBuckets[$cityKey])) {
                    $cityBuckets[$cityKey] = ['name' => ucwords($cityKey), 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0];
                }
                $cityBuckets[$cityKey]['transactions']++;
                $cityBuckets[$cityKey]['revenue'] += $amount;
                $cityBuckets[$cityKey]['guests'] += $guests;
            }

            $state = trim((string) ($transaction->payment_state ?? ''));
            if ($state !== '') {
                $stateKey = strtoupper($state);
                if (!isset($stateBuckets[$stateKey])) {
                    $stateBuckets[$stateKey] = ['name' => $stateKey, 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0];
                }
                $stateBuckets[$stateKey]['transactions']++;
                $stateBuckets[$stateKey]['revenue'] += $amount;
                $stateBuckets[$stateKey]['guests'] += $guests;
            }

            $country = trim((string) ($transaction->payment_country ?? ''));
            if ($country !== '') {
                $countryKey = strtoupper($country);
                if (!isset($countryBuckets[$countryKey])) {
                    $countryBuckets[$countryKey] = ['name' => $countryKey, 'transactions' => 0, 'revenue' => 0.0, 'guests' => 0];
                }
                $countryBuckets[$countryKey]['transactions']++;
                $countryBuckets[$countryKey]['revenue'] += $amount;
                $countryBuckets[$countryKey]['guests'] += $guests;
            }

            foreach ($orderValueBands as $idx => $band) {
                if (array_key_exists('exact', $band)) {
                    $inRange = abs($amount - (float) $band['exact']) < 0.00001;
                } else {
                    $inRange = $band['max'] === null
                        ? ($amount >= $band['min'])
                        : ($amount >= $band['min'] && $amount <= $band['max']);
                }

                if ($inRange) {
                    $orderValueBands[$idx]['transactions']++;
                    $orderValueBands[$idx]['revenue'] += $amount;
                    break;
                }
            }

            if (!empty($transaction->package_use_date)) {
                $useDate = Carbon::parse($transaction->package_use_date, $timezone)->startOfDay();
                $leadDays = $createdAtLocal->startOfDay()->diffInDays($useDate, false);
                if ($leadDays >= 0) {
                    $leadTimeDays[] = $leadDays;
                    foreach ($leadTimeBands as $idx => $band) {
                        if (!isset($band['min'])) {
                            continue;
                        }
                        $inRange = $band['max'] === null
                            ? ($leadDays >= $band['min'])
                            : ($leadDays >= $band['min'] && $leadDays <= $band['max']);

                        if ($inRange) {
                            $leadTimeBands[$idx]['transactions']++;
                            break;
                        }
                    }
                } else {
                    foreach ($leadTimeBands as $idx => $band) {
                        if (($band['special'] ?? null) === 'past_use') {
                            $leadTimeBands[$idx]['transactions']++;
                            break;
                        }
                    }
                }
            } else {
                foreach ($leadTimeBands as $idx => $band) {
                    if (($band['special'] ?? null) === 'no_use_date') {
                        $leadTimeBands[$idx]['transactions']++;
                        break;
                    }
                }
            }

            if (!empty($transaction->checked_in_at_pacific)) {
                $checkinAtLocal = $transaction->checked_in_at_pacific->copy()->timezone($timezone);
                $diffMinutes = $createdAtLocal->diffInMinutes($checkinAtLocal, false);
                if ($diffMinutes >= 0) {
                    $checkinLagMinutes[] = $diffMinutes;
                }
            }
        }

        $hourlyTrend = collect($hourlyBuckets)->values();
        $weekdayTrend = collect($weekdayBuckets)->values();
        $topCities = collect($cityBuckets)->sortByDesc('transactions')->take(12)->values();
        $topStates = collect($stateBuckets)->sortByDesc('transactions')->take(10)->values();
        $topCountries = collect($countryBuckets)->sortByDesc('transactions')->take(8)->values();
        $orderValueBands = collect($orderValueBands);
        $leadTimeBands = collect($leadTimeBands);

        $peakHourByTransactions = $hourlyTrend->sortByDesc('transactions')->first();
        $peakHourByRevenue = $hourlyTrend->sortByDesc('revenue')->first();
        $largestTransaction = $transactions->sortByDesc(fn ($t) => (float) ($t->total ?? 0))->first();

        $avgLeadDays = count($leadTimeDays) > 0
            ? (array_sum($leadTimeDays) / count($leadTimeDays))
            : 0;
        $avgCheckinLagMinutes = count($checkinLagMinutes) > 0
            ? (array_sum($checkinLagMinutes) / count($checkinLagMinutes))
            : 0;

        $sourceSnapshot = [
            'direct' => [
                'transactions' => (int) $transactions->filter(fn ($t) => empty($t->affiliate_id) && empty($t->entertainer_id))->count(),
                'revenue' => (float) $transactions->filter(fn ($t) => empty($t->affiliate_id) && empty($t->entertainer_id))->sum('total'),
            ],
            'affiliate' => [
                'transactions' => (int) $transactions->filter(fn ($t) => !empty($t->affiliate_id))->count(),
                'revenue' => (float) $transactions->filter(fn ($t) => !empty($t->affiliate_id))->sum('total'),
            ],
            'entertainer' => [
                'transactions' => (int) $transactions->filter(fn ($t) => !empty($t->entertainer_id))->count(),
                'revenue' => (float) $transactions->filter(fn ($t) => !empty($t->entertainer_id))->sum('total'),
            ],
        ];

        $summary = [
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'unique_patrons' => $uniquePatrons,
            'total_guests' => $totalGuests,
            'avg_order_value' => $avgOrder,
            'max_order_value' => (float) $transactions->max('total'),
            'min_order_value' => (float) $transactions->min('total'),
            'total_addons_qty' => (int) $totalAddonsQty,
            'affiliate_commission' => $affiliateCommission,
            'entertainer_commission' => $entertainerCommission,
            'total_commission' => $totalCommission,
            'net_revenue' => $totalRevenue - $totalCommission,
            'avg_lead_days' => $avgLeadDays,
            'avg_checkin_lag_minutes' => $avgCheckinLagMinutes,
            'transactions_with_addons' => $transactionsWithAddons,
            'addon_attach_rate' => $totalTransactions > 0 ? (($transactionsWithAddons / $totalTransactions) * 100) : 0,
            'zero_value_transactions' => (int) (collect($orderValueBands)->firstWhere('label', '$0')['transactions'] ?? 0),
            'zero_value_share' => $totalTransactions > 0
                ? (((int) (collect($orderValueBands)->firstWhere('label', '$0')['transactions'] ?? 0) / $totalTransactions) * 100)
                : 0,
            'total_sessions' => $totalSessions,
            'unique_visitors' => $uniqueVisitors,
            'avg_pages_per_session' => $averagePagesPerSession,
            'avg_session_duration_seconds' => $averageSessionDurationSeconds,
            'bounced_sessions' => $bouncedSessions,
            'bounce_rate' => $bounceRate,
        ];

        $insights = [
            'peak_hour_transactions' => $peakHourByTransactions,
            'peak_hour_revenue' => $peakHourByRevenue,
            'largest_transaction' => $largestTransaction ? [
                'id' => (int) $largestTransaction->id,
                'amount' => (float) ($largestTransaction->total ?? 0),
                'created_at' => $largestTransaction->created_at
                    ? $largestTransaction->created_at->copy()->timezone($timezone)->format('M d, Y h:i A')
                    : null,
                'website_name' => optional($largestTransaction->website)->name,
            ] : null,
        ];

        $payload = [
            'summary' => $summary,
            'clubSnapshot' => $clubSnapshot,
            'topPackages' => $topPackages,
            'dailyTrend' => $dailyTrend,
            'hourlyTrend' => $hourlyTrend,
            'weekdayTrend' => $weekdayTrend,
            'topCities' => $topCities,
            'topStates' => $topStates,
            'topCountries' => $topCountries,
            'orderValueBands' => $orderValueBands,
            'leadTimeBands' => $leadTimeBands,
            'topAddons' => $topAddons,
            'topPackageAddonCombinations' => $topPackageAddonCombinations,
            'sourceSnapshot' => $sourceSnapshot,
            'transportSnapshot' => $transportSnapshot,
            'packageModeSnapshot' => $packageModeSnapshot,
            'sessionSnapshot' => $sessionSnapshot,
            'insights' => $insights,
            'timezone' => $timezone,
            'periodLabel' => $periodLabel,
            'startAt' => $startAt,
            'endAt' => $endAt,
            'selectedWebsites' => $selectedWebsites,
            'generatedAt' => now(),
        ];

        if ($request->boolean('interactive')) {
            return view('admin.reports.automation-executive-interactive', $payload);
        }

        $pdf = Pdf::loadView('admin.reports.automation-executive-pdf', $payload)
            ->setPaper('a4', 'portrait');

        $filename = 'automation_executive_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->stream($filename);
    }

    private function resolveAutomationDateRange(Request $request, string $timezone): array
    {
        $period = strtolower((string) $request->get('period', 'weekly'));
        $dateRange = strtolower((string) $request->get('date_range', ''));
        $customFrom = $request->get('custom_from', $request->get('from'));
        $customTo = $request->get('custom_to', $request->get('to'));
        $now = now($timezone);

        // First priority: explicit custom date windows sent by automation dispatch links.
        if (($dateRange === 'custom' || (!empty($customFrom) && !empty($customTo))) && !empty($customFrom) && !empty($customTo)) {
            $startAt = Carbon::parse($customFrom, $timezone)->startOfDay();
            $endAt = Carbon::parse($customTo, $timezone)->endOfDay();

            $label = match ($period) {
                'daily' => 'Daily',
                'monthly' => 'Monthly',
                'yearly' => 'Yearly',
                'weekly' => 'Weekly',
                default => 'Custom',
            };

            return [$startAt, $endAt, $label];
        }

        if ($period === 'daily') {
            $startAt = $now->copy()->subDay()->startOfDay();
            $endAt = $now->copy()->subDay()->endOfDay();
            return [$startAt, $endAt, 'Daily'];
        }

        if ($period === 'monthly') {
            $startAt = $now->copy()->subMonthNoOverflow()->startOfMonth();
            $endAt = $startAt->copy()->endOfMonth();
            return [$startAt, $endAt, 'Monthly'];
        }

        if ($period === 'yearly') {
            $startAt = $now->copy()->subYear()->startOfYear();
            $endAt = $startAt->copy()->endOfYear();
            return [$startAt, $endAt, 'Yearly'];
        }

        if ($period === 'custom') {
            $from = $request->get('from', $customFrom);
            $to = $request->get('to', $customTo);
            $startAt = $from ? Carbon::parse($from, $timezone)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
            $endAt = $to ? Carbon::parse($to, $timezone)->endOfDay() : $now->copy()->endOfDay();
            return [$startAt, $endAt, 'Custom'];
        }

        // Weekly default
        $startAt = $now->copy()->subDays(6)->startOfDay();
        $endAt = $now->copy()->endOfDay();
        return [$startAt, $endAt, 'Weekly'];
    }

    private function resolveAutomationTimezone(Request $request): string
    {
        $tz = trim((string) $request->get('timezone', 'America/Los_Angeles'));
        if ($tz === '') {
            return 'America/Los_Angeles';
        }

        try {
            new \DateTimeZone($tz);
            return $tz;
        } catch (\Throwable $e) {
            return 'America/Los_Angeles';
        }
    }

    private function resolveAutomationWebsiteIds(Request $request): array
    {
        $raw = $request->get('website_ids', []);

        if (is_string($raw)) {
            $parts = array_filter(array_map('trim', explode(',', $raw)));
            $ids = array_map('intval', $parts);
        } elseif (is_array($raw)) {
            $ids = array_map('intval', $raw);
        } else {
            $ids = [];
        }

        $ids = array_values(array_unique(array_filter($ids, fn ($v) => $v > 0)));

        $user = auth()->user();
        if (!empty($user?->website_id)) {
            return [(int) $user->website_id];
        }

        return $ids;
    }

    public function automationSchedules(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $accessibleWebsiteIds = $this->resolveUserAccessibleWebsiteIds($user);
        $websites = Website::query()
            ->whereIn('id', $accessibleWebsiteIds)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        $schedulesQuery = AutomationReportSchedule::query()->with('creator');
        if (!$user->isAdmin()) {
            $schedulesQuery->where('created_by_user_id', $user->id);
        }

        $schedules = $schedulesQuery->orderByDesc('created_at')->get();

        return view('admin.reports.automations.index', [
            'schedules' => $schedules,
            'websites' => $websites,
            'defaultTimezone' => 'America/Los_Angeles',
        ]);
    }

    public function automationSchedulesStore(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $validated = $this->validateAutomationSchedule($request, $user);

        $schedule = new AutomationReportSchedule();
        $schedule->fill($validated);
        $schedule->created_by_user_id = $user->id;
        $schedule->is_active = true;
        $schedule->next_run_at = $this->automationScheduler->computeNextRunAt($schedule);
        $schedule->save();

        return redirect()
            ->route('admin.reports.automation.schedules')
            ->with('success', 'Automation schedule created successfully.');
    }

    public function automationSchedulesEdit(AutomationReportSchedule $schedule)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->assertCanManageSchedule($schedule, $user);

        $accessibleWebsiteIds = $this->resolveUserAccessibleWebsiteIds($user);
        $websites = Website::query()
            ->whereIn('id', $accessibleWebsiteIds)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.reports.automations.edit', [
            'schedule' => $schedule,
            'websites' => $websites,
            'defaultTimezone' => 'America/Los_Angeles',
        ]);
    }

    public function automationSchedulesUpdate(AutomationReportSchedule $schedule, Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->assertCanManageSchedule($schedule, $user);
        $validated = $this->validateAutomationSchedule($request, $user);

        $schedule->fill($validated);
        $schedule->next_run_at = $this->automationScheduler->computeNextRunAt($schedule);
        $schedule->save();

        return redirect()
            ->route('admin.reports.automation.schedules')
            ->with('success', 'Automation schedule updated successfully.');
    }

    public function automationSchedulesToggle(AutomationReportSchedule $schedule)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->assertCanManageSchedule($schedule, $user);

        $schedule->is_active = !$schedule->is_active;
        if ($schedule->is_active) {
            $schedule->next_run_at = $this->automationScheduler->computeNextRunAt($schedule);
        }
        $schedule->save();

        return back()->with('success', 'Schedule status updated.');
    }

    public function automationSchedulesDestroy(AutomationReportSchedule $schedule)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->assertCanManageSchedule($schedule, $user);
        $schedule->delete();

        return back()->with('success', 'Schedule deleted successfully.');
    }

    public function automationSchedulesRunNow(AutomationReportSchedule $schedule)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $this->assertCanManageSchedule($schedule, $user);
        $this->dispatchAutomationSchedule($schedule, $user->id);

        return back()->with('success', 'Automation report generated and sent successfully.');
    }

    public function automationHistory(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $runsQuery = AutomationReportRun::query()->with(['schedule', 'triggeredBy']);
        if (!$user->isAdmin()) {
            $runsQuery->whereHas('schedule', function ($q) use ($user) {
                $q->where('created_by_user_id', $user->id);
            });
        }

        $runs = $runsQuery->orderByDesc('id')->paginate(25);

        return view('admin.reports.automations.history', [
            'runs' => $runs,
        ]);
    }

    private function validateAutomationSchedule(Request $request, $user): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:daily,weekly,monthly,yearly',
            'report_period_type' => 'required|in:daily,weekly,monthly,yearly,custom_range',
            'website_ids' => 'required|array|min:1',
            'website_ids.*' => 'integer|exists:websites,id',
            'email_recipients' => 'required',
            'send_time' => 'nullable|date_format:H:i',
            'one_time_date' => 'nullable|date',
            'one_time_time' => 'nullable|date_format:H:i',
            'weekly_day' => 'nullable|integer|min:0|max:6',
            'monthly_day' => 'nullable|integer|min:1|max:31',
            'yearly_month' => 'nullable|integer|min:1|max:12',
            'yearly_day' => 'nullable|integer|min:1|max:31',
            'custom_from_month' => 'nullable|date',
            'custom_to_month' => 'nullable|date',
        ]);

        $requestedWebsiteIds = collect($validated['website_ids'])->map(fn ($id) => (int) $id)->unique()->values()->all();
        $allowedWebsiteIds = $this->resolveUserAccessibleWebsiteIds($user);
        $invalidWebsiteIds = array_values(array_diff($requestedWebsiteIds, $allowedWebsiteIds));
        if (!empty($invalidWebsiteIds)) {
            abort(403, 'You cannot select one or more of the chosen clubs.');
        }

        $rawRecipients = $request->input('email_recipients', []);
        if (is_array($rawRecipients)) {
            $emails = collect($rawRecipients)
                ->map(fn ($email) => strtolower(trim((string) $email)))
                ->filter()
                ->unique()
                ->values();
        } else {
            $emails = collect(preg_split('/[,;\s]+/', (string) $rawRecipients))
                ->filter()
                ->map(fn ($email) => strtolower(trim((string) $email)))
                ->unique()
                ->values();
        }

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()
                    ->withErrors(['email_recipients' => 'One or more email addresses are invalid.'])
                    ->withInput()
                    ->throwResponse();
            }
        }

        if ($validated['frequency'] === 'weekly' && is_null($validated['weekly_day'])) {
            return back()->withErrors(['weekly_day' => 'Week day is required for weekly schedules.'])->withInput()->throwResponse();
        }

        if ($validated['frequency'] === 'monthly' && is_null($validated['monthly_day'])) {
            return back()->withErrors(['monthly_day' => 'Day of month is required for monthly schedules.'])->withInput()->throwResponse();
        }

        if ($validated['frequency'] === 'yearly' && (is_null($validated['yearly_month']) || is_null($validated['yearly_day']))) {
            return back()->withErrors(['yearly_day' => 'Month and day are required for yearly schedules.'])->withInput()->throwResponse();
        }

        if ($validated['report_period_type'] === 'custom_range' && (empty($validated['custom_from_month']) || empty($validated['custom_to_month']))) {
            return back()->withErrors(['custom_from_month' => 'From/To dates are required for custom range report type.'])->withInput()->throwResponse();
        }

        $customFrom = null;
        $customTo = null;
        if (!empty($validated['custom_from_month'])) {
            $customFrom = Carbon::parse($validated['custom_from_month'])->toDateString();
        }
        if (!empty($validated['custom_to_month'])) {
            $customTo = Carbon::parse($validated['custom_to_month'])->toDateString();
        }

        if ($customFrom && $customTo && $customFrom > $customTo) {
            return back()->withErrors(['custom_to_month' => 'To month must be after or equal to From month.'])->withInput()->throwResponse();
        }

        return [
            'name' => $validated['name'],
            'frequency' => $validated['frequency'],
            'report_period_type' => $validated['report_period_type'],
            'website_ids' => $requestedWebsiteIds,
            'email_recipients' => $emails->all(),
            'timezone' => 'America/Los_Angeles',
            'send_time' => !empty($validated['send_time']) ? ($validated['send_time'] . ':00') : '06:00:00',
            'one_time_date' => !empty($validated['one_time_date']) ? Carbon::parse($validated['one_time_date'])->toDateString() : null,
            'one_time_time' => !empty($validated['one_time_time']) ? ($validated['one_time_time'] . ':00') : null,
            'weekly_day' => $validated['frequency'] === 'weekly' ? (int) $validated['weekly_day'] : null,
            'monthly_day' => $validated['frequency'] === 'monthly' ? (int) $validated['monthly_day'] : null,
            'yearly_month' => $validated['frequency'] === 'yearly' ? (int) $validated['yearly_month'] : null,
            'yearly_day' => $validated['frequency'] === 'yearly' ? (int) $validated['yearly_day'] : null,
            'custom_from_month' => $validated['report_period_type'] === 'custom_range' ? $customFrom : null,
            'custom_to_month' => $validated['report_period_type'] === 'custom_range' ? $customTo : null,
        ];
    }

    private function resolveUserAccessibleWebsiteIds($user): array
    {
        return collect($user->accessibleWebsiteIds())
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function assertCanManageSchedule(AutomationReportSchedule $schedule, $user): void
    {
        if ($user->isAdmin()) {
            return;
        }

        if ((int) $schedule->created_by_user_id !== (int) $user->id) {
            abort(403, 'You are not allowed to manage this automation schedule.');
        }
    }

    public function dispatchDueAutomationSchedules(): array
    {
        $now = now();
        $dueSchedules = AutomationReportSchedule::query()
            ->where('is_active', true)
            ->whereNotNull('next_run_at')
            ->where('next_run_at', '<=', $now)
            ->orderBy('next_run_at')
            ->limit(50)
            ->get();

        $result = [
            'processed' => 0,
            'sent' => 0,
            'failed' => 0,
        ];

        foreach ($dueSchedules as $schedule) {
            $result['processed']++;
            $run = $this->dispatchAutomationSchedule($schedule, null);
            if ($run->status === 'sent') {
                $result['sent']++;
            } else {
                $result['failed']++;
            }
        }

        return $result;
    }

    private function dispatchAutomationSchedule(AutomationReportSchedule $schedule, ?int $triggeredByUserId): AutomationReportRun
    {
        $rangePayload = $this->automationScheduler->buildRangePayload($schedule, now());
        $params = array_merge($rangePayload, [
            'website_ids' => $schedule->website_ids ?? [],
            'timezone' => $schedule->timezone ?: 'America/Los_Angeles',
        ]);

        $run = AutomationReportRun::create([
            'automation_report_schedule_id' => $schedule->id,
            'triggered_by_user_id' => $triggeredByUserId,
            'status' => 'pending',
            'email_recipients' => $schedule->email_recipients ?? [],
            'website_ids' => $schedule->website_ids ?? [],
            'report_params' => $params,
        ]);

        try {
            $signedUrl = URL::temporarySignedRoute(
                'reports.automation.publicPreviewSigned',
                now()->addDays(7),
                $params
            );

            $subject = 'Automation Report: ' . $schedule->name;
            $body = "Your automated report is ready.\n\n";
            $body .= "Schedule: {$schedule->name}\n";
            $body .= "Frequency: {$schedule->frequency}\n";
            $body .= "Generated at: " . now()->format('Y-m-d H:i:s') . "\n\n";
            $body .= "Preview/Download link:\n{$signedUrl}\n\n";
            $body .= "This link will expire in 7 days.";

            foreach ((array) ($schedule->email_recipients ?? []) as $recipient) {
                Mail::raw($body, function ($message) use ($recipient, $subject) {
                    $message->to($recipient)->subject($subject);
                });
            }

            $run->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $schedule->last_run_at = now();
            $schedule->last_run_status = 'sent';
            $schedule->last_error = null;
            $schedule->next_run_at = $this->automationScheduler->computeNextRunAt($schedule, now()->addMinute());
            $schedule->save();
        } catch (\Throwable $e) {
            $run->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $schedule->last_run_at = now();
            $schedule->last_run_status = 'failed';
            $schedule->last_error = $e->getMessage();
            $schedule->next_run_at = $this->automationScheduler->computeNextRunAt($schedule, now()->addMinute());
            $schedule->save();
        }

        return $run;
    }

    /**
     * Get report metadata and available filters
     */
    public function metadata(Report $report)
    {
        $user = auth()->user();

        if (!$report->canAccessBy($user)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'report' => [
                'id' => $report->id,
                'name' => $report->name,
                'slug' => $report->slug,
                'description' => $report->description,
                'category' => $report->category,
                'type' => $report->type,
                'available_filters' => $report->available_filters,
                'default_date_range' => $report->default_date_range,
            ],
        ]);
    }

    /**
     * Get list of reports by category
     */
    public function byCategory($category, Request $request)
    {
        $user = auth()->user();

        $reports = Report::accessibleBy($user)
            ->where('category', $category)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'data' => $reports,
            ]);
        }

        return view('admin.reports.index', [
            'selectedCategory' => $category,
            'reports' => $reports,
            'categories' => Report::where('is_active', true)->distinct('category')->pluck('category'),
        ]);
    }

    /**
     * Delete saved report preference
     */
    public function deletePreference($preferenceId)
    {
        $user = auth()->user();

        $preference = UserReportPreference::findOrFail($preferenceId);

        // Authorization check
        if ($preference->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $preference->delete();

        return response()->json([
            'success' => true,
            'message' => 'Saved report deleted successfully',
        ]);
    }
}
