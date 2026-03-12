<?php

namespace App\Http\Controllers;

use App\Mail\AffiliateApprovedMail;
use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\Package;
use App\Models\SMTP;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AffiliateAdminController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only super admin can manage affiliates.');
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();
        $status = $request->input('status', 'pending');

        $query = Affiliate::with('user');
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $affiliates = $query->latest()->get();

        return view('admin.affiliate.index', compact('affiliates', 'status'));
    }

    public function show(Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $affiliate->load(['user', 'affiliatePackages.package.website']);
        $websites = Website::where('is_archieved', 0)->with(['packages' => function ($query) {
            $query->where('status', 1);
        }])->get();

        return view('admin.affiliate.show', compact('affiliate', 'websites'));
    }

    public function approve(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'default_commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $affiliate->status = 'approved';
        $affiliate->is_active = true;
        $affiliate->approved_at = now();
        $affiliate->approved_by = auth()->id();
        $affiliate->default_commission_percentage = $request->default_commission_percentage;
        if (empty($affiliate->slug)) {
            $affiliate->slug = Affiliate::generateUniqueSlug($affiliate->display_name ?: $affiliate->user->name);
        }
        $affiliate->save();

        try {
            $this->applyGlobalSmtp();
            Mail::to($affiliate->user->email)->send(new AffiliateApprovedMail($affiliate));
        } catch (\Throwable $th) {
            // Keep approval successful even if mail fails.
        }

        return redirect()->back()->with('success', 'Affiliate approved successfully.');
    }

    public function reject(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $affiliate->status = 'rejected';
        $affiliate->rejection_reason = $request->rejection_reason;
        $affiliate->is_active = false;
        $affiliate->save();

        return redirect()->back()->with('success', 'Affiliate application rejected.');
    }

    public function updatePackages(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'integer|exists:packages,id',
            'commissions' => 'nullable|array',
        ]);

        $packageIds = collect($request->input('package_ids', []))->map(fn ($id) => (int) $id)->unique()->values();

        AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereNotIn('package_id', $packageIds->all())
            ->delete();

        foreach ($packageIds as $packageId) {
            $package = Package::with('website')->find($packageId);
            if (!$package || !$package->website) {
                continue;
            }

            $commission = $request->input('commissions.' . $packageId, $affiliate->default_commission_percentage);
            $commission = is_numeric($commission) ? max(0, min(100, (float) $commission)) : (float) $affiliate->default_commission_percentage;

            AffiliatePackage::updateOrCreate(
                [
                    'affiliate_id' => $affiliate->id,
                    'package_id' => $packageId,
                ],
                [
                    'website_id' => $package->website->id,
                    'commission_percentage' => $commission,
                    'is_active' => true,
                ]
            );
        }

        return redirect()->back()->with('success', 'Affiliate packages and commissions updated successfully.');
    }

    private function applyGlobalSmtp(): void
    {
        $smtp = SMTP::latest()->first();
        if (!$smtp || empty($smtp->host) || empty($smtp->port) || empty($smtp->username) || empty($smtp->password)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->host,
            'mail.mailers.smtp.port' => $smtp->port,
            'mail.mailers.smtp.username' => $smtp->username,
            'mail.mailers.smtp.password' => $smtp->password,
            'mail.mailers.smtp.encryption' => in_array($smtp->encryption, ['tls', 'ssl'], true) ? $smtp->encryption : ((string) $smtp->encryption === '1' ? 'tls' : null),
            'mail.from.address' => $smtp->from_email ?: config('mail.from.address'),
            'mail.from.name' => $smtp->from_name ?: config('mail.from.name'),
        ]);
    }
}
