<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;

class PaymentSettingsController extends Controller
{
    /**
     * Show payment settings for a website
     */
    public function edit(Website $website): View
    {
        // Check authorization
        if (!$this->canEditWebsite($website)) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.website.payment-settings', [
            'website' => $website,
        ]);
    }

    /**
     * Update payment settings for a website
     */
    public function update(Website $website, Request $request): RedirectResponse
    {
        // Check authorization
        if (!$this->canEditWebsite($website)) {
            abort(403, 'Unauthorized action.');
        }

        // Validate payment settings
        $validated = $request->validate([
            'payment_method' => 'required|in:authorize,stripe',
            'stripe_app_key' => 'nullable|string',
            'stripe_secret_key' => 'nullable|string',
            'stripe_public_key' => 'nullable|string',
            'authorize_app_key' => 'nullable|string',
            'authorize_secret_key' => 'nullable|string',
            'authorize_login_id' => 'nullable|string',
            'authorize_transaction_key' => 'nullable|string',
            'sandbox_mode' => 'nullable|boolean',
            'gratuity_fee' => 'nullable|numeric|min:0',
            'gratuity_name' => 'nullable|string|max:255',
            'refundable_fee' => 'nullable|numeric|min:0',
            'refundable_name' => 'nullable|string|max:255',
            'sales_tax_fee' => 'nullable|numeric|min:0',
            'sales_tax_name' => 'nullable|string|max:255',
            'service_charge_fee' => 'nullable|numeric|min:0',
            'service_charge_name' => 'nullable|string|max:255',
            'processing_fee' => 'nullable|numeric|min:0',
            'processing_fee_type' => 'required_with:processing_fee|in:percentage,flat',
            'promo_code_name' => 'nullable|string|max:255',
            'commission_hold_days' => 'nullable|integer|min:0|max:365',
            'commission_hold_days_authorize' => 'nullable|integer|min:0|max:365',
        ]);

        // Keep existing API keys when fields are intentionally left blank during updates.
        $secretLikeFields = [
            'stripe_public_key',
            'stripe_app_key',
            'stripe_secret_key',
            'authorize_login_id',
            'authorize_transaction_key',
            'authorize_app_key',
            'authorize_secret_key',
        ];

        foreach ($secretLikeFields as $field) {
            if ($request->has($field) && trim((string) $request->input($field)) === '') {
                unset($validated[$field]);
            }
        }

        // In checkout logic, charge lines are disabled when field name is "0".
        foreach (['gratuity_name', 'sales_tax_name', 'service_charge_name'] as $chargeFieldName) {
            if (array_key_exists($chargeFieldName, $validated) && trim((string) $validated[$chargeFieldName]) === '') {
                $validated[$chargeFieldName] = '0';
            }
        }

        // Keep optional display labels clean when left empty.
        foreach (['refundable_name', 'promo_code_name'] as $optionalNameField) {
            if (array_key_exists($optionalNameField, $validated) && trim((string) $validated[$optionalNameField]) === '') {
                $validated[$optionalNameField] = null;
            }
        }

        // An unchecked checkbox is not submitted, so set the per-website sandbox
        // flag explicitly when the column exists: checked => sandbox (true),
        // unchecked => live (false). If the column is missing in an older DB
        // schema, skip it to keep current flow working.
        if (Schema::hasColumn('websites', 'sandbox_mode')) {
            $validated['sandbox_mode'] = $request->boolean('sandbox_mode');
        } else {
            unset($validated['sandbox_mode']);
        }

        // Update website with payment settings
        $website->update($validated);

        return redirect()->route('admin.website.edit', $website->id)
            ->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Check if user can edit website
     */
    private function canEditWebsite(Website $website): bool
    {
        $user = auth()->user();
        
        // Super admin can edit any website
        if ($user->isAdmin()) {
            return true;
        }

        // Website user / bouncer / manager can edit the website(s) they're scoped to.
        return in_array((int) $website->id, $user->accessibleWebsiteIds(), true);
    }
}
