<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrFormConfig;

class NightlyFormBuilderController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $reportType = $request->input('type', 'nightly');
        $configs = NrFormConfig::where('report_type', $reportType)
            ->orderBy('sort_order')
            ->get();

        return view('admin.nightly-reports.form-builder.index', compact('configs', 'reportType'));
    }

    public function update(Request $request)
    {
        $fields = $request->input('fields', []);

        foreach ($fields as $id => $data) {
            $config = NrFormConfig::find($id);
            if ($config) {
                $config->update([
                    'label' => $data['label'] ?? $config->label,
                    'visible' => isset($data['visible']),
                    'required' => isset($data['required']),
                    'sort_order' => (int) ($data['sort_order'] ?? $config->sort_order),
                    'hint' => $data['hint'] ?? null,
                ]);
            }
        }

        return back()->with('success', 'Form configuration saved successfully.');
    }
}
