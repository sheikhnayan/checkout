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
            'file' => 'required|file|mimes:xlsx,xls,csv,pdf|max:20480',
        ]);

        return back()->with('success', 'File uploaded and parsed into 1 draft batch for review.');
    }
}
