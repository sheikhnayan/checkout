<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateWalletTransaction;
use App\Models\Entertainer;
use App\Models\EntertainerWalletTransaction;
use App\Models\Setting;
use App\Models\WithdrawPayoutMethod;
use App\Models\WithdrawRequest;
use App\Services\CommissionLifecycleRunner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    // ------------------------------------------------------------------ helpers

    /**
     * Returns ['owner' => model, 'type' => 'affiliate'|'entertainer'] or aborts.
     */
    private function resolveOwner(): array
    {
        $user = auth()->user();

        if ($user->isAffiliate()) {
            $owner = $user->affiliate;
            if (!$owner || $owner->status !== 'approved' || !$owner->is_active) {
                abort(403, 'affiliate access denied.');
            }
            return ['owner' => $owner, 'type' => 'affiliate'];
        }

        if ($user->isEntertainer()) {
            $owner = $user->entertainer;
            if (!$owner || $owner->status !== 'approved' || !$owner->is_active) {
                abort(403, 'Entertainer access denied.');
            }
            return ['owner' => $owner, 'type' => 'entertainer'];
        }

        abort(403);
    }

    /**
     * Get current withdraw charge percentage for the owner.
     */
    private function chargeFor(array $resolved): float
    {
        if ($resolved['type'] === 'affiliate') {
            $setting = Setting::first();
            return $setting ? (float) $setting->affiliate_withdraw_charge : 0.0;
        }

        // entertainer: from their website
        $websiteId = $resolved['owner']->website_id;
        if ($websiteId) {
            $website = \App\Models\Website::find($websiteId);
            return $website ? (float) $website->withdraw_charge : 0.0;
        }

        return 0.0;
    }

    // ------------------------------------------------------------------ portal pages

    /**
     * GET /affiliate-portal/withdraw  or  /entertainer-portal/withdraw
     */
    public function index()
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $resolved = $this->resolveOwner();
        $owner    = $resolved['owner'];
        $type     = $resolved['type'];

        $payoutMethods = WithdrawPayoutMethod::forOwner($owner->id, $type)->latest()->get();
        $requests      = WithdrawRequest::forOwner($owner->id, $type)->latest()->paginate(15);
        $charge        = $this->chargeFor($resolved);
        $typeLabels    = WithdrawPayoutMethod::typeLabels();

        $view = $type === 'affiliate' ? 'affiliate.withdraw' : 'entertainer.withdraw';

        return view($view, compact('owner', 'payoutMethods', 'requests', 'charge', 'typeLabels'));
    }

    // ------------------------------------------------------------------ payout methods

    /**
     * POST /affiliate-portal/withdraw/methods  or  /entertainer-portal/withdraw/methods
     */
    public function storeMethod(Request $request)
    {
        $resolved = $this->resolveOwner();
        $owner    = $resolved['owner'];
        $type     = $resolved['type'];

        $validated = $request->validate([
            'label'   => 'required|string|max:100',
            'type'    => 'required|in:bank_transfer,wire,check,paypal,zelle,other',
            'details' => 'required|array',
            'details.*' => 'nullable|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            WithdrawPayoutMethod::forOwner($owner->id, $type)->update(['is_default' => false]);
        }

        WithdrawPayoutMethod::create([
            'owner_id'   => $owner->id,
            'owner_type' => $type,
            'label'      => $validated['label'],
            'type'       => $validated['type'],
            'details'    => $validated['details'],
            'is_default' => !empty($validated['is_default']),
        ]);

        return redirect()->back()->with('success', 'Payout method saved.');
    }

    /**
     * POST /affiliate-portal/withdraw/methods/{id}/delete
     */
    public function destroyMethod(int $id)
    {
        $resolved = $this->resolveOwner();
        $owner    = $resolved['owner'];
        $type     = $resolved['type'];

        $method = WithdrawPayoutMethod::forOwner($owner->id, $type)->findOrFail($id);
        $method->delete();

        return redirect()->back()->with('success', 'Payout method removed.');
    }

    /**
     * POST /affiliate-portal/withdraw/methods/{id}/set-default
     */
    public function setDefaultMethod(int $id)
    {
        $resolved = $this->resolveOwner();
        $owner    = $resolved['owner'];
        $type     = $resolved['type'];

        WithdrawPayoutMethod::forOwner($owner->id, $type)->update(['is_default' => false]);
        WithdrawPayoutMethod::forOwner($owner->id, $type)->findOrFail($id)->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default payout method updated.');
    }

    // ------------------------------------------------------------------ withdraw requests

    /**
     * POST /affiliate-portal/withdraw/request  or  /entertainer-portal/withdraw/request
     */
    public function storeRequest(Request $request)
    {
        $resolved = $this->resolveOwner();
        $owner    = $resolved['owner'];
        $type     = $resolved['type'];

        $validated = $request->validate([
            'amount'           => 'required|numeric|min:1',
            'payout_method_id' => 'required|integer',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $amount = (float) $validated['amount'];

        // Verify method belongs to this owner
        $method = WithdrawPayoutMethod::forOwner($owner->id, $type)
            ->findOrFail($validated['payout_method_id']);

        // Check sufficient balance
        if ($amount > (float) $owner->wallet_balance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient wallet balance. Your balance is $' . number_format($owner->wallet_balance, 2) . '.');
        }

        $charge     = $this->chargeFor($resolved);
        $feeAmount  = round($amount * $charge / 100, 2);
        $netAmount  = round($amount - $feeAmount, 2);
        $websiteId  = $type === 'entertainer' ? $owner->website_id : null;

        DB::transaction(function () use ($owner, $type, $method, $amount, $charge, $feeAmount, $netAmount, $websiteId, $validated) {
            // Deduct from wallet immediately
            $newBalance = (float) $owner->wallet_balance - $amount;
            $owner->wallet_balance = $newBalance;
            $owner->save();

            // Log wallet transaction
            $description = 'Withdrawal request of $' . number_format($amount, 2)
                . ($feeAmount > 0 ? ' (fee: $' . number_format($feeAmount, 2) . ')' : '');

            if ($type === 'affiliate') {
                AffiliateWalletTransaction::create([
                    'affiliate_id'  => $owner->id,
                    'type'          => 'withdrawal',
                    'status'        => 'pending',
                    'amount'        => -$amount,
                    'balance_after' => $newBalance,
                    'description'   => $description,
                ]);
            } else {
                EntertainerWalletTransaction::create([
                    'entertainer_id' => $owner->id,
                    'type'           => 'withdrawal',
                    'status'         => 'pending',
                    'amount'         => -$amount,
                    'balance_after'  => $newBalance,
                    'description'    => $description,
                ]);
            }

            // Create withdraw request
            WithdrawRequest::create([
                'owner_id'         => $owner->id,
                'owner_type'       => $type,
                'payout_method_id' => $method->id,
                'website_id'       => $websiteId,
                'amount'           => $amount,
                'fee_percentage'   => $charge,
                'fee_amount'       => $feeAmount,
                'net_amount'       => $netAmount,
                'status'           => 'pending',
                'notes'            => $validated['notes'] ?? null,
                'method_snapshot'  => [
                    'label'   => $method->label,
                    'type'    => $method->type,
                    'details' => $method->details,
                ],
            ]);
        });

        return redirect()->back()->with('success', 'Withdrawal request submitted. $' . number_format($amount, 2) . ' has been deducted from your wallet.');
    }

    // ------------------------------------------------------------------ admin: affiliate withdrawals

    /**
     * GET /admins/withdraw/affiliates
     */
    public function adminAffiliates(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $status  = $request->input('status', 'all');
        $query   = WithdrawRequest::with(['payoutMethod'])
            ->where('owner_type', 'affiliate');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(25)->withQueryString();

        // Attach affiliate display names
        $affiliateIds = $requests->pluck('owner_id')->unique();
        $affiliates   = \App\Models\Affiliate::whereIn('id', $affiliateIds)->get()->keyBy('id');

        // Global charge setting
        $setting = Setting::first();

        return view('admin.withdraw.affiliates', compact('requests', 'affiliates', 'status', 'setting'));
    }

    /**
     * POST /admins/withdraw/affiliates/{id}/status
     */
    public function adminAffiliateStatus(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'status'      => 'required|in:pending,done,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $wr = WithdrawRequest::where('owner_type', 'affiliate')->findOrFail($id);

        // If rejecting a pending request, refund the wallet
        if ($validated['status'] === 'rejected' && $wr->status === 'pending') {
            DB::transaction(function () use ($wr, $validated) {
                $affiliate  = \App\Models\Affiliate::findOrFail($wr->owner_id);
                $newBalance = (float) $affiliate->wallet_balance + (float) $wr->amount;
                $affiliate->wallet_balance = $newBalance;
                $affiliate->save();

                AffiliateWalletTransaction::create([
                    'affiliate_id'  => $affiliate->id,
                    'type'          => 'withdrawal_refund',
                    'status'        => 'completed',
                    'amount'        => $wr->amount,
                    'balance_after' => $newBalance,
                    'description'   => 'Withdrawal request #' . $wr->id . ' rejected – amount refunded.',
                ]);

                $wr->status      = $validated['status'];
                $wr->admin_notes = $validated['admin_notes'] ?? null;
                $wr->save();
            });
        } else {
            $wr->status      = $validated['status'];
            $wr->admin_notes = $validated['admin_notes'] ?? null;
            $wr->save();
        }

        return redirect()->back()->with('success', 'Withdrawal status updated to "' . $validated['status'] . '".');
    }

    /**
     * POST /admins/withdraw/affiliates/charge
     */
    public function adminAffiliateCharge(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'affiliate_withdraw_charge' => 'required|numeric|min:0|max:100',
        ]);

        $setting = Setting::first();
        if ($setting) {
            $setting->affiliate_withdraw_charge = $request->affiliate_withdraw_charge;
            $setting->save();
        }

        return redirect()->back()->with('success', 'Global affiliate withdrawal charge updated.');
    }

    // ------------------------------------------------------------------ admin: entertainer withdrawals

    /**
     * GET /admins/withdraw/entertainers
     */
    public function adminEntertainers(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isWebsiteUser()) {
            abort(403);
        }

        $status = $request->input('status', 'all');
        $query  = WithdrawRequest::with(['payoutMethod', 'website'])
            ->where('owner_type', 'entertainer');

        // Website users only see their website's requests
        if ($user->isWebsiteUser() && $user->website_id) {
            $query->where('website_id', $user->website_id);
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests      = $query->latest()->paginate(25)->withQueryString();
        $entertainerIds = $requests->pluck('owner_id')->unique();
        $entertainers  = \App\Models\Entertainer::whereIn('id', $entertainerIds)->get()->keyBy('id');

        // Withdraw charge for context
        $website = null;
        if ($user->isWebsiteUser() && $user->website_id) {
            $website = \App\Models\Website::find($user->website_id);
        }

        $websites = $user->isAdmin()
            ? \App\Models\Website::where('is_archieved', 0)->where('status', 1)->orderBy('name')->get()
            : collect();

        return view('admin.withdraw.entertainers', compact('requests', 'entertainers', 'status', 'website', 'websites'));
    }

    /**
     * POST /admins/withdraw/entertainers/{id}/status
     */
    public function adminEntertainerStatus(Request $request, int $id)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isWebsiteUser()) {
            abort(403);
        }

        $validated = $request->validate([
            'status'      => 'required|in:pending,done,rejected',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $query = WithdrawRequest::where('owner_type', 'entertainer');
        if ($user->isWebsiteUser() && $user->website_id) {
            $query->where('website_id', $user->website_id);
        }
        $wr = $query->findOrFail($id);

        if ($validated['status'] === 'rejected' && $wr->status === 'pending') {
            DB::transaction(function () use ($wr, $validated) {
                $entertainer = \App\Models\Entertainer::findOrFail($wr->owner_id);
                $newBalance  = (float) $entertainer->wallet_balance + (float) $wr->amount;
                $entertainer->wallet_balance = $newBalance;
                $entertainer->save();

                EntertainerWalletTransaction::create([
                    'entertainer_id' => $entertainer->id,
                    'type'           => 'withdrawal_refund',
                    'status'         => 'completed',
                    'amount'         => $wr->amount,
                    'balance_after'  => $newBalance,
                    'description'    => 'Withdrawal request #' . $wr->id . ' rejected – amount refunded.',
                ]);

                $wr->status      = $validated['status'];
                $wr->admin_notes = $validated['admin_notes'] ?? null;
                $wr->save();
            });
        } else {
            $wr->status      = $validated['status'];
            $wr->admin_notes = $validated['admin_notes'] ?? null;
            $wr->save();
        }

        return redirect()->back()->with('success', 'Withdrawal status updated to "' . $validated['status'] . '".');
    }

    /**
     * POST /admins/withdraw/entertainers/charge
     */
    public function adminEntertainerCharge(Request $request)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !($user->isWebsiteUser() && $user->website_id)) {
            abort(403);
        }

        $request->validate([
            'website_id'      => 'required|integer',
            'withdraw_charge' => 'required|numeric|min:0|max:100',
        ]);

        // Super admin can update any website; website user only their own
        $websiteId = (int) $request->website_id;
        if ($user->isWebsiteUser() && $user->website_id !== $websiteId) {
            abort(403);
        }

        \App\Models\Website::where('id', $websiteId)->update([
            'withdraw_charge' => $request->withdraw_charge,
        ]);

        return redirect()->back()->with('success', 'Withdraw charge updated for the website.');
    }
}
