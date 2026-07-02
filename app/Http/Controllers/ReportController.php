<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UserReportPreference;
use App\Models\ReportExport;
use App\Models\Transaction;
use App\Models\Website;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
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
        $now = now($timezone);

        if ($period === 'daily') {
            $startAt = $now->copy()->subDay()->startOfDay();
            $endAt = $now->copy()->subDay()->endOfDay();
            return [$startAt, $endAt, 'Daily'];
        }

        if ($period === 'custom') {
            $from = $request->get('from');
            $to = $request->get('to');
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
