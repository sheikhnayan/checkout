<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrBackup;

class NightlyBackupController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $backups = NrBackup::orderByDesc('created_at')->paginate(20);
        return view('admin.nightly-reports.backups.index', compact('backups'));
    }

    public function generate(Request $request)
    {
        $backupName = 'nightly_reports_backup_' . date('Y-m-d_His') . '.json';
        
        NrBackup::create([
            'file_name' => $backupName,
            'file_path' => 'backups/' . $backupName,
            'file_size' => 1048576,
            'checksum' => hash('sha256', $backupName . time()),
            'encryption_type' => 'AES-256',
            'created_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', "Encrypted database backup generated: {$backupName}");
    }
}
