<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AnalyticsV2Service;
use App\Models\Website;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsV2Controller extends Controller
{
    public function index(Request $request)
    {
        $venues = Website::query()->where('is_archieved', 0)->orderBy('name')->get();
        $filters = [
            'period' => $request->get('period', 'last_30_days'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'website_id' => $request->get('website_id'),
        ];

        $service = new AnalyticsV2Service($filters);
        $payload = $service->getFullPayload();

        return view('admin.analytics_v2.index', [
            'venues' => $venues,
            'filters' => $filters,
            'payload' => $payload,
        ]);
    }

    public function getData(Request $request)
    {
        $filters = [
            'period' => $request->get('period', 'last_30_days'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'website_id' => $request->get('website_id'),
        ];

        $service = new AnalyticsV2Service($filters);
        return response()->json($service->getFullPayload());
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $module = $request->get('module', 'executive_pulse');
        $filters = [
            'period' => $request->get('period', 'last_30_days'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'website_id' => $request->get('website_id'),
        ];

        $service = new AnalyticsV2Service($filters);
        $payload = $service->getFullPayload();

        $rows = match ($module) {
            'revenue_waterfall' => $payload['revenue_waterfall']['table'],
            'gateway_matrix' => $payload['gateway_matrix']['table'],
            'venue_heatmap' => $payload['venue_heatmap']['table'],
            'affiliate_attribution' => $payload['affiliate_attribution']['table'],
            'entertainer_performance' => $payload['entertainer_performance']['table'],
            'geospatial_analytics' => $payload['geospatial_analytics']['table'],
            default => $payload['venue_heatmap']['table'],
        };

        $filename = "analytics_v2_{$module}_" . date('Y-m-d_His') . ".csv";

        return new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');
            if (!empty($rows)) {
                fputcsv($handle, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($handle, array_values($row));
                }
            }
            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
