<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrLegalToken;
use App\Models\NightlyReports\NrLocation;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NightlyLegalController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $tokens = NrLegalToken::orderByDesc('created_at')->paginate(20);
        $locations = NrLocation::where('active', true)->orderBy('name')->get();

        return view('admin.nightly-reports.legal.index', compact('tokens', 'locations'));
    }

    public function createToken(Request $request)
    {
        $validated = $request->validate([
            'attorney_name' => 'required|string|max:150',
            'firm_name' => 'nullable|string|max:150',
            'case_reference' => 'nullable|string|max:100',
            'days_valid' => 'required|integer|min:1|max:90',
            'location_ids' => 'nullable|array',
        ]);

        $tokenStr = Str::random(48);

        NrLegalToken::create([
            'token' => $tokenStr,
            'attorney_name' => $validated['attorney_name'],
            'firm_name' => $validated['firm_name'] ?? null,
            'case_reference' => $validated['case_reference'] ?? null,
            'location_ids' => $validated['location_ids'] ?? null,
            'expires_at' => Carbon::now()->addDays($validated['days_valid']),
            'created_by_user_id' => auth()->id(),
        ]);

        return back()->with('success', "Legal access token generated: {$tokenStr}");
    }

    public function revokeToken($id)
    {
        $token = NrLegalToken::findOrFail($id);
        $token->revoked = true;
        $token->save();

        return back()->with('success', "Access token revoked successfully.");
    }
}
