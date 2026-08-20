<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrDocumentRequest;
use Carbon\Carbon;

class NightlyDocumentRequestController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = NrDocumentRequest::query();
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.nightly-reports.document-requests.index', compact('requests', 'status'));
    }

    public function approve(Request $request, $id)
    {
        $docReq = NrDocumentRequest::findOrFail($id);
        $docReq->update([
            'status' => 'approved',
            'reviewed_at' => Carbon::now(),
            'reviewed_by' => auth()->user()->name ?? 'Legal Officer',
            'reviewer_note' => $request->input('note'),
        ]);

        return back()->with('success', 'Document request approved.');
    }

    public function deny(Request $request, $id)
    {
        $docReq = NrDocumentRequest::findOrFail($id);
        $docReq->update([
            'status' => 'denied',
            'reviewed_at' => Carbon::now(),
            'reviewed_by' => auth()->user()->name ?? 'Legal Officer',
            'reviewer_note' => $request->input('note'),
        ]);

        return back()->with('success', 'Document request denied.');
    }
}
