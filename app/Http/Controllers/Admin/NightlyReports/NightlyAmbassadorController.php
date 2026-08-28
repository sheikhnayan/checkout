<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use App\Http\Controllers\Controller;
use App\Models\NightlyReportAmbassador;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NightlyAmbassadorController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $ambassadors = NightlyReportAmbassador::with('clubs')->get();
            $websites = Website::all();
        } else {
            $ambassadors = NightlyReportAmbassador::with('clubs')->where('created_by_user_id', $user->id)->get();
            // In a real scenario, this would be websites the user has access to. For now, we'll fetch all or implement based on existing logic.
            // Assuming Website::all() for the dropdown, but you may want to scope it.
            $websites = Website::all(); 
        }

        return view('admin.nightly-reports.ambassadors.index', compact('ambassadors', 'websites'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:nightly_report_ambassadors,email',
            'clubs' => 'array'
        ]);

        $token = Str::random(60);

        DB::beginTransaction();
        try {
            $ambassador = NightlyReportAmbassador::create([
                'name' => $request->name,
                'email' => $request->email,
                'created_by_user_id' => Auth::id(),
                'setup_token' => $token,
                'is_active' => true,
            ]);

            if ($request->has('clubs')) {
                $ambassador->clubs()->sync($request->clubs);
            }

            Mail::to($ambassador->email)->send(new AmbassadorSetupEmail($ambassador));
            Log::info('Nightly report ambassador setup email sent', [
                'ambassador_id' => $ambassador->id,
                'email' => $ambassador->email,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Ambassador created successfully and setup email sent.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Nightly report ambassador setup email failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            return redirect()->back()->with('error', 'Error creating ambassador: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $ambassador = NightlyReportAmbassador::findOrFail($id);
        
        // Ensure user can edit this ambassador
        if (!Auth::user()->isSuperAdmin() && $ambassador->created_by_user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $request->validate([
            'clubs' => 'array'
        ]);

        $ambassador->clubs()->sync($request->clubs ?? []);
        $ambassador->is_active = $request->has('is_active');
        $ambassador->save();

        return redirect()->back()->with('success', 'Ambassador updated successfully.');
    }

    public function impersonate($id)
    {
        $ambassador = NightlyReportAmbassador::findOrFail($id);
        
        // Ensure user can impersonate this ambassador
        if (!Auth::user()->isSuperAdmin() && $ambassador->created_by_user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        Auth::guard('ambassador')->login($ambassador);
        return redirect()->route('ambassador.dashboard');
    }
}
