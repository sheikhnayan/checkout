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

        $params = $request->only(['period', 'from', 'to', 'website_ids']);

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
        [$startAt, $endAt, $periodLabel] = $this->resolveAutomationDateRange($request);
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
        foreach ($transactions as $transaction) {
            $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : [];
            foreach ($cartItems as $item) {
                if (!is_array($item) || !isset($item['addons']) || !is_array($item['addons'])) {
                    continue;
                }
                foreach ($item['addons'] as $addon) {
                    if (!is_array($addon)) {
                        continue;
                    }
                    $totalAddonsQty += max(1, (int) ($addon['qty'] ?? $addon['quantity'] ?? 1));
                }
            }
        }

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
                return [
                    'package_name' => (string) $label,
                    'transactions' => (int) $items->count(),
                    'revenue' => (float) $items->sum('total'),
                    'guests' => (int) $items->sum(fn ($t) => max(1, (int) ($t->package_number_of_guest ?? 1))),
                ];
            })
            ->sortByDesc('revenue')
            ->take(12)
            ->values();

        $dailyTrend = Transaction::query()
            ->financiallyReportable()
            ->whereBetween('created_at', [$startAt, $endAt])
            ->when(!empty($websiteIds), fn ($q) => $q->whereIn('website_id', $websiteIds))
            ->select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('COUNT(*) as transactions'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('SUM(COALESCE(package_number_of_guest, 1)) as guests'),
                DB::raw('COUNT(DISTINCT package_email) as unique_patrons')
            )
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'day' => (string) $row->day,
                'transactions' => (int) $row->transactions,
                'revenue' => (float) $row->revenue,
                'guests' => (int) $row->guests,
                'unique_patrons' => (int) $row->unique_patrons,
            ]);

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
            'total_addons_qty' => (int) $totalAddonsQty,
            'affiliate_commission' => $affiliateCommission,
            'entertainer_commission' => $entertainerCommission,
            'total_commission' => $totalCommission,
            'net_revenue' => $totalRevenue - $totalCommission,
        ];

        $payload = [
            'summary' => $summary,
            'clubSnapshot' => $clubSnapshot,
            'topPackages' => $topPackages,
            'dailyTrend' => $dailyTrend,
            'sourceSnapshot' => $sourceSnapshot,
            'periodLabel' => $periodLabel,
            'startAt' => $startAt,
            'endAt' => $endAt,
            'selectedWebsites' => $selectedWebsites,
            'generatedAt' => now(),
        ];

        $pdf = Pdf::loadView('admin.reports.automation-executive-pdf', $payload)
            ->setPaper('a4', 'portrait');

        $filename = 'automation_executive_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->stream($filename);
    }

    private function resolveAutomationDateRange(Request $request): array
    {
        $period = strtolower((string) $request->get('period', 'weekly'));
        $now = now('America/Los_Angeles');

        if ($period === 'daily') {
            $startAt = $now->copy()->subDay()->startOfDay();
            $endAt = $now->copy()->subDay()->endOfDay();
            return [$startAt, $endAt, 'Daily'];
        }

        if ($period === 'custom') {
            $from = $request->get('from');
            $to = $request->get('to');
            $startAt = $from ? Carbon::parse($from, 'America/Los_Angeles')->startOfDay() : $now->copy()->subDays(6)->startOfDay();
            $endAt = $to ? Carbon::parse($to, 'America/Los_Angeles')->endOfDay() : $now->copy()->endOfDay();
            return [$startAt, $endAt, 'Custom'];
        }

        // Weekly default
        $startAt = $now->copy()->subDays(6)->startOfDay();
        $endAt = $now->copy()->endOfDay();
        return [$startAt, $endAt, 'Weekly'];
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
