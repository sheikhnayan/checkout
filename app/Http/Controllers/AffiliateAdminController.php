<?php

namespace App\Http\Controllers;

use App\Mail\AffiliateApprovedMail;
use App\Mail\AdminApplicationNotificationMail;
use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\AffiliateWebsite;
use App\Models\Package;
use App\Models\SMTP;
use App\Models\Transaction;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AffiliateAdminController extends Controller
{
    private function ensureAdmin(): void
    {
        $user = auth()->user();
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Only super admin can manage promoters.');
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

        $promoters = $query->latest()->get();

        return view('admin.affiliate.index', compact('promoters', 'status'));
    }

    public function show(Affiliate $affiliate)
    {
        $this->ensureAdmin();

        // Ensure relationships are loaded
        if (!$affiliate->relationLoaded('user')) {
            $affiliate->load('user');
        }
        if (!$affiliate->relationLoaded('affiliateWebsites')) {
            $affiliate->load('affiliateWebsites.website');
        }

        // Get websites with package counts
        $websites = Website::where('is_archieved', 0)
            ->where('status', 1)
            ->withCount(['packages' => function ($query) {
                $query->where('status', 1)->where('is_archieved', 0);
            }])
            ->get();

        // Get selected website IDs
        $selectedWebsiteIds = $affiliate->affiliateWebsites()
            ->where('is_active', true)
            ->pluck('website_id')
            ->toArray();

        // Calculate commission amounts
        $now = now();
        $transactions = Transaction::query()
            ->where('affiliate_id', $affiliate->id)
            ->get();

        $pendingAmount = $transactions->sum(function ($t) use ($now) {
            $isPending = (string) ($t->affiliate_commission_status ?? '') === Transaction::COMMISSION_STATUS_PENDING;
            $holdUntil = $t->affiliate_commission_hold_until;

            if (!$isPending || !$holdUntil || $holdUntil->lte($now)) {
                return 0;
            }

            return (float) ($t->affiliate_commission_amount ?? 0);
        });

        $payoutAmount = $transactions
            ->where('affiliate_commission_status', Transaction::COMMISSION_STATUS_PAID)
            ->sum(fn ($t) => (float) ($t->affiliate_commission_amount ?? 0));

        $totalEarning = $transactions
            ->reject(fn ($t) => (string) ($t->affiliate_commission_status ?? '') === Transaction::COMMISSION_STATUS_REVERSED)
            ->sum(fn ($t) => (float) ($t->affiliate_commission_amount ?? 0));

        return view('admin.affiliate.show', compact(
            'affiliate',
            'websites',
            'selectedWebsiteIds',
            'pendingAmount',
            'payoutAmount',
            'totalEarning',
            'transactions'
        ));
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
            Mail::to('hello@cartvip.com')->send(new AdminApplicationNotificationMail(
                'Promoter',
                'approved',
                $affiliate->user->name,
                $affiliate->user->email,
                $affiliate->affiliateWebsites->first()?->website->name ?? '',
            ));
        } catch (\Throwable $th) {
            // Keep approval successful even if mail fails.
        }

        return redirect()->back()->with('success', 'Promoter approved successfully.');
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

        try {
            $this->applyGlobalSmtp();
            Mail::to('hello@cartvip.com')->send(new AdminApplicationNotificationMail(
                'Promoter',
                'rejected',
                $affiliate->user->name,
                $affiliate->user->email,
                $affiliate->affiliateWebsites->first()?->website->name ?? '',
                $affiliate->rejection_reason
            ));
        } catch (\Throwable $th) {
            // Keep rejection successful even if mail fails.
        }

        return redirect()->back()->with('success', 'Promoter application rejected.');
    }

    public function unapprove(Affiliate $affiliate)
    {
        $this->ensureAdmin();

        $affiliate->status = 'pending';
        $affiliate->is_active = false;
        $affiliate->approved_at = null;
        $affiliate->approved_by = null;
        $affiliate->rejection_reason = null;
        $affiliate->save();

        return redirect()->back()->with('success', 'Promoter has been unapproved and moved back to pending review.');
    }

    public function updatePackages(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'website_ids' => 'nullable|array',
            'website_ids.*' => 'integer|exists:websites,id',
        ]);

        $websiteIds = collect($request->input('website_ids', []))->map(fn ($id) => (int) $id)->unique()->values();

        AffiliateWebsite::where('affiliate_id', $affiliate->id)
            ->whereNotIn('website_id', $websiteIds->all())
            ->delete();

        foreach ($websiteIds as $websiteId) {
            AffiliateWebsite::updateOrCreate(
                [
                    'affiliate_id' => $affiliate->id,
                    'website_id' => $websiteId,
                ],
                [
                    'is_active' => true,
                ]
            );
        }

        // Remove previously selected package mappings from clubs no longer assigned.
        AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereNotIn('website_id', $websiteIds->all())
            ->delete();

        return redirect()->back()->with('success', 'Promoter club access updated successfully.');
    }

    public function updateCommission(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();

        $request->validate([
            'default_commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $affiliate->default_commission_percentage = $request->default_commission_percentage;
        $affiliate->save();

        return redirect()->back()->with('success', 'Promoter commission updated successfully.');
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
