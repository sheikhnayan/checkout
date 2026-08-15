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
use Illuminate\Support\Facades\Log;

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

        $query = Affiliate::with('user', 'approved_by_user', 'rejected_by_user', 'parent');
        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $affiliates = $query->latest()->get();

        $parentAffiliates = Affiliate::where('status', 'approved')
            ->where(function($q) {
                $q->where('is_sub_affiliate', false)->orWhereNull('is_sub_affiliate');
            })
            ->with('user')
            ->get();

        $allWebsites = Website::where('is_archieved', 0)
            ->where('status', 1)
            ->with(['packages' => function ($q) {
                $q->clubVisible()->where('status', 1)->where('is_archieved', 0);
            }])
            ->get();

        return view('admin.affiliate.index', compact('affiliates', 'status', 'parentAffiliates', 'allWebsites'));
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

        // Ensure relationships are loaded
        if (!$affiliate->relationLoaded('user')) {
            $affiliate->load('user');
        }
        if (!$affiliate->relationLoaded('affiliateWebsites')) {
            $affiliate->load('affiliateWebsites.website');
        }

        $affiliate->status = 'approved';
        $affiliate->is_active = true;
        $affiliate->approved_at = now();
        $affiliate->approved_by = auth()->id();
        $affiliate->rejected_at = null;
        $affiliate->rejected_by = null;
        $affiliate->rejection_reason = null;
        $affiliate->default_commission_percentage = $request->default_commission_percentage;
        if (empty($affiliate->slug)) {
            $affiliate->slug = Affiliate::generateUniqueSlug($affiliate->display_name ?: $affiliate->user->name);
        }
        $affiliate->save();

        try {
            $this->applyGlobalSmtp();

            // Send approval email to affiliate
            if ($affiliate->user && $affiliate->user->email) {
                Mail::to($affiliate->user->email)->send(new AffiliateApprovedMail($affiliate));
            }

            // Send notification to admin
            Mail::to('hello@cartvip.com')->send(new AdminApplicationNotificationMail(
                'affiliate',
                'approved',
                $affiliate->user->name ?? 'Unknown',
                $affiliate->user->email ?? 'unknown@cartvip.com',
                $affiliate->affiliateWebsites->first()?->website->name ?? '',
            ));
        } catch (\Throwable $th) {
            Log::error('Affiliate approval email failed: ' . $th->getMessage(), [
                'affiliate_id' => $affiliate->id,
                'email' => $affiliate->user?->email,
                'exception' => (string) $th,
            ]);
        }

        return redirect()->back()->with('success', 'affiliate approved successfully.');
    }

    public function reject(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        // Ensure relationships are loaded
        if (!$affiliate->relationLoaded('user')) {
            $affiliate->load('user');
        }
        if (!$affiliate->relationLoaded('affiliateWebsites')) {
            $affiliate->load('affiliateWebsites.website');
        }

        $affiliate->status = 'rejected';
        $affiliate->rejection_reason = $request->rejection_reason;
        $affiliate->is_active = false;
        $affiliate->rejected_at = now();
        $affiliate->rejected_by = auth()->id();
        $affiliate->save();

        try {
            $this->applyGlobalSmtp();
            Mail::to('hello@cartvip.com')->send(new AdminApplicationNotificationMail(
                'affiliate',
                'rejected',
                $affiliate->user->name ?? 'Unknown',
                $affiliate->user->email ?? 'unknown@cartvip.com',
                $affiliate->affiliateWebsites->first()?->website->name ?? '',
                null,
                $affiliate->rejection_reason
            ));
        } catch (\Throwable $th) {
            Log::error('Affiliate rejection email failed: ' . $th->getMessage(), [
                'affiliate_id' => $affiliate->id,
                'email' => $affiliate->user?->email,
                'exception' => (string) $th,
            ]);
        }

        return redirect()->back()->with('success', 'promoter application rejected.');
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

        return redirect()->back()->with('success', 'affiliate has been unapproved and moved back to pending review.');
    }

    public function updatePackages(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();
        $request->validate([
            'website_ids' => 'nullable|array',
            'website_ids.*' => 'integer|exists:websites,id',
            'club_commissions' => 'nullable|array',
            'club_commissions.*' => 'nullable|numeric|min:0|max:100',
        ]);

        $websiteIds = collect($request->input('website_ids', []))->map(fn ($id) => (int) $id)->unique()->values();
        $clubCommissions = $request->input('club_commissions', []);

        AffiliateWebsite::where('affiliate_id', $affiliate->id)
            ->whereNotIn('website_id', $websiteIds->all())
            ->delete();

        foreach ($websiteIds as $websiteId) {
            $commission = isset($clubCommissions[$websiteId]) && $clubCommissions[$websiteId] !== '' && $clubCommissions[$websiteId] !== null
                ? (float) $clubCommissions[$websiteId]
                : null;

            AffiliateWebsite::updateOrCreate(
                [
                    'affiliate_id' => $affiliate->id,
                    'website_id' => $websiteId,
                ],
                [
                    'is_active' => true,
                    'commission_percentage' => $commission,
                ]
            );
        }

        // Remove previously selected package mappings from clubs no longer assigned.
        AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->whereNotIn('website_id', $websiteIds->all())
            ->delete();

        return redirect()->back()->with('success', 'Promoter club access & dynamic commissions updated successfully.');
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

    public function storeSubAffiliate(Request $request)
    {
        $this->ensureAdmin();

        $request->validate([
            'parent_affiliate_id' => 'required|exists:affiliates,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'display_name' => 'nullable|string|max:255',
            'website_ids' => 'nullable|array',
            'package_ids' => 'nullable|array',
        ]);

        $parent = Affiliate::findOrFail($request->input('parent_affiliate_id'));
        $requireForm = $request->boolean('require_onboarding_form');

        // Create User
        $user = \App\Models\User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'user_type' => 'affiliate',
        ]);

        $displayName = $request->input('display_name') ?: $request->input('name');
        $slug = Affiliate::generateUniqueSlug($displayName);

        // Create Sub-Affiliate
        $sub = Affiliate::create([
            'user_id' => $user->id,
            'parent_affiliate_id' => $parent->id,
            'is_sub_affiliate' => true,
            'display_name' => $displayName,
            'slug' => $slug,
            'status' => $requireForm ? 'pending' : 'approved',
            'is_active' => true,
            'approved_at' => $requireForm ? null : now(),
            'approved_by' => $requireForm ? null : auth()->id(),
            'require_onboarding_form' => $requireForm,
            'sub_affiliate_permissions' => [
                'show_packages' => $request->boolean('show_packages', true),
                'show_settings' => $request->boolean('show_settings', true),
                'show_qr_code' => $request->boolean('show_qr_code', true),
                'show_sales_stats' => $request->boolean('show_sales_stats', true),
            ],
        ]);

        // Allocate Websites
        if ($request->has('website_ids')) {
            foreach ($request->input('website_ids', []) as $webId) {
                AffiliateWebsite::create([
                    'affiliate_id' => $sub->id,
                    'website_id' => $webId,
                    'is_active' => true,
                ]);
            }
        }

        // Allocate Packages
        if ($request->has('package_ids')) {
            foreach ($request->input('package_ids', []) as $pkgId) {
                $pkg = Package::find($pkgId);
                AffiliatePackage::create([
                    'affiliate_id' => $sub->id,
                    'package_id' => $pkgId,
                    'website_id' => $pkg->website_id ?? null,
                    'commission_percentage' => 0,
                    'is_active' => true,
                ]);
            }
        }

        // Email Notification
        try {
            $this->applyGlobalSmtp();
            $subject = "Welcome to CartVIP Promoter Portal";
            if ($requireForm) {
                $onboardUrl = route('entertainer.apply');
                $html = "<div style='font-family:sans-serif;padding:20px;'><h3 style='color:#4f46e5;'>Welcome {$user->name}</h3><p>You have been added as a sub-promoter under <strong>{$parent->display_name}</strong>.</p><p>Please complete your onboarding application form to activate your portal: <a href='{$onboardUrl}' style='color:#4f46e5;font-weight:bold;'>Complete Form</a></p><p>Login Email: <strong>{$user->email}</strong></p></div>";
            } else {
                $loginUrl = route('login');
                $html = "<div style='font-family:sans-serif;padding:20px;'><h3 style='color:#4f46e5;'>Welcome {$user->name}</h3><p>You have been added as an active sub-promoter under <strong>{$parent->display_name}</strong>.</p><p>Login Email: <strong>{$user->email}</strong><br>Password: <strong>{$request->input('password')}</strong></p><p><a href='{$loginUrl}' style='color:#4f46e5;font-weight:bold;'>Click here to login</a></p></div>";
            }
            Mail::html($html, function($msg) use ($user, $subject) {
                $msg->to($user->email)->subject($subject);
            });
        } catch (\Exception $e) {
            Log::error('Admin sub-affiliate email error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Sub-promoter created successfully by Admin!');
    }

    public function updateSubAffiliatePermissions(Request $request, Affiliate $affiliate)
    {
        $this->ensureAdmin();

        $affiliate->sub_affiliate_permissions = [
            'show_packages' => $request->boolean('show_packages'),
            'show_settings' => $request->boolean('show_settings'),
            'show_qr_code' => $request->boolean('show_qr_code'),
            'show_sales_stats' => $request->boolean('show_sales_stats'),
        ];
        $affiliate->require_onboarding_form = $request->boolean('require_onboarding_form');
        $affiliate->save();

        return redirect()->back()->with('success', 'Sub-promoter dashboard toggles updated successfully by Admin!');
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
