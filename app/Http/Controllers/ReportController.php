<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\UserReportPreference;
use App\Models\ReportExport;
use App\Services\ReportGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        // This would use a library like dompdf
        // For now, return JSON with error
        return response()->json(['message' => 'PDF export coming soon'], 202);
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
