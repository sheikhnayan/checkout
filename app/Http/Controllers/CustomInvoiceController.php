<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomInvoice;
use App\Models\CustomInvoiceItem;
use App\Models\Website;
use App\Models\Setting;
use App\Models\SMTP;
use App\Mail\CustomInvoiceMail;
use App\Mail\CustomInvoicePaymentConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Stripe;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class CustomInvoiceController extends Controller
{
    private function authorizeCustomInvoiceAccess($websiteId, string $message = 'Unauthorized'): void
    {
        $user = auth()->user();
        if ($user && $user->isAdmin()) {
            return;
        }

        if (!$user || !$user->canCreateCustomInvoiceOnWebsite($websiteId)) {
            abort(403, $message);
        }
    }

    /**
     * Display a listing of custom invoices
     */
    public function index()
    {
        $user = auth()->user();
        $includeArchived = request()->boolean('include_archived');
        
        if ($user->isAdmin()) {
            $invoices = CustomInvoice::with(['website', 'items'])
                                    ->when(!$includeArchived, function ($query) {
                                        $query->whereNull('archived_at');
                                    })
                                    ->latest()
                                    ->get();
        } elseif ($user->isAffiliate() || $user->isEntertainer() || $user->affiliate || $user->entertainer) {
            // Promoters, Sub-promoters, and Entertainers only see custom invoices created by themselves.
            $invoices = CustomInvoice::where('user_id', $user->id)
                                    ->when(!$includeArchived, function ($query) {
                                        $query->whereNull('archived_at');
                                    })
                                    ->with(['website', 'items'])
                                    ->latest()
                                    ->get();
        } else {
            $accessibleIds = $user->accessibleCustomInvoiceWebsiteIds();
            $invoices = CustomInvoice::whereIn('website_id', $accessibleIds)
                                    ->when(!$includeArchived, function ($query) {
                                        $query->whereNull('archived_at');
                                    })
                                    ->with(['website', 'items'])
                                    ->latest()
                                    ->get();
        }

        return view('admin.custom-invoice.index', compact('invoices', 'includeArchived'));
    }

    /**
     * Show the form for creating a new custom invoice
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $websites = Website::all();
        } else {
            $accessibleIds = $user->accessibleCustomInvoiceWebsiteIds();
            $websites = Website::whereIn('id', $accessibleIds)->get();
        }

        return view('admin.custom-invoice.create', compact('websites'));
    }

    /**
     * Store a newly created custom invoice
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'website_id' => 'required|exists:websites,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Check authorization for custom invoice creation
        $this->authorizeCustomInvoiceAccess($request->website_id, 'Unauthorized');

        $invoice = new CustomInvoice();
        $invoice->user_id = $user->id;
        $invoice->website_id = $request->website_id;
        $invoice->client_name = $request->client_name;
        $invoice->client_email = $request->client_email;
        $invoice->notes = $request->notes;
        $invoice->internal_notes = $request->internal_notes;
        $invoice->payment_token = CustomInvoice::generatePaymentToken();
        $invoice->save();

        // Add items
        foreach ($request->items as $itemData) {
            CustomInvoiceItem::create([
                'custom_invoice_id' => $invoice->id,
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        }

        // Calculate totals
        $invoice->calculateTotals();
        $invoice->save();

        \App\Services\ActivityLogger::log(
            'create',
            "Created custom invoice #{$invoice->invoice_number} for client {$invoice->client_name} (\${$invoice->total_amount}).",
            'custom_invoices',
            $invoice,
            $invoice->website_id
        );

        // Check if we should send immediately
        if ($request->input('action') === 'send') {
            try {
                $this->applyInvoiceSmtpConfig($invoice, $user);

                Mail::to($invoice->client_email)->send(
                    new CustomInvoiceMail($invoice)
                );

                $invoice->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);

                return redirect()->route('admin.custom-invoice.show', $invoice->id)
                               ->with('success', 'Custom invoice created and sent successfully!');
            } catch (\Exception $e) {
                return redirect()->route('admin.custom-invoice.show', $invoice->id)
                               ->with('warning', 'Invoice created but failed to send: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.custom-invoice.show', $invoice->id)
                       ->with('success', 'Custom invoice created successfully!');
    }

    /**
     * Display the specified custom invoice
     */
    public function show(CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        return view('admin.custom-invoice.show', compact('customInvoice'));
    }

    /**
     * Show the form for editing the specified custom invoice
     */
    public function edit(CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if ($customInvoice->archived_at) {
            return redirect()->back()->with('error', 'Archived invoices cannot be edited. Please restore it first.');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only edit draft invoices!');
        }

        $websites = $user->isAdmin()
                    ? Website::all()
                    : Website::whereIn('id', $user->accessibleCustomInvoiceWebsiteIds())->get();

        return view('admin.custom-invoice.edit', compact('customInvoice', 'websites'));
    }

    /**
     * Update the specified custom invoice
     */
    public function update(Request $request, CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if ($customInvoice->archived_at) {
            return redirect()->back()->with('error', 'Archived invoices cannot be updated. Please restore it first.');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only edit draft invoices!');
        }

        $request->validate([
            'website_id' => 'required|exists:websites,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $customInvoice->update([
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'notes' => $request->notes,
            'internal_notes' => $request->internal_notes,
            'website_id' => $request->website_id,
        ]);

        // Delete old items and create new ones
        $customInvoice->items()->delete();
        foreach ($request->items as $itemData) {
            CustomInvoiceItem::create([
                'custom_invoice_id' => $customInvoice->id,
                'name' => $itemData['name'],
                'price' => $itemData['price'],
                'quantity' => $itemData['quantity'] ?? 1,
            ]);
        }

        // Recalculate totals
        $customInvoice->calculateTotals();
        $customInvoice->save();

        return redirect()->route('admin.custom-invoice.show', $customInvoice->id)
                       ->with('success', 'Custom invoice updated successfully!');
    }

    /**
     * Send the custom invoice to client
     */
    public function send(CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if ($customInvoice->archived_at) {
            return redirect()->back()->with('error', 'Archived invoices cannot be sent. Please restore it first.');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only send draft invoices!');
        }

        try {
            $this->applyInvoiceSmtpConfig($customInvoice, $user);

            Mail::to($customInvoice->client_email)->send(
                new CustomInvoiceMail($customInvoice)
            );

            $customInvoice->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Invoice sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to send invoice: ' . $e->getMessage());
        }
    }

    /**
     * Delete the specified custom invoice
     */
    public function destroy(CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if ($customInvoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot delete paid invoices!');
        }

        $customInvoice->delete();
        return redirect()->route('admin.custom-invoice.index')->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Archive the specified custom invoice
     */
    public function archive(CustomInvoice $customInvoice)
    {
        $user = auth()->user();

        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if ($customInvoice->archived_at) {
            return redirect()->back()->with('info', 'Invoice is already archived.');
        }

        $customInvoice->archived_at = now();
        $customInvoice->save();

        return redirect()->route('admin.custom-invoice.index')->with('success', 'Invoice archived successfully!');
    }

    /**
     * Restore an archived custom invoice
     */
    public function unarchive(CustomInvoice $customInvoice)
    {
        $user = auth()->user();

        $this->authorizeCustomInvoiceAccess($customInvoice->website_id, 'Unauthorized');

        if (!$customInvoice->archived_at) {
            return redirect()->back()->with('info', 'Invoice is not archived.');
        }

        $customInvoice->archived_at = null;
        $customInvoice->save();

        return redirect()->route('admin.custom-invoice.index', ['include_archived' => 1])->with('success', 'Invoice restored successfully!');
    }

    /**
     * Configure mail transport for invoice emails with fallback strategy.
     */
    private function applyInvoiceSmtpConfig(CustomInvoice $invoice, $user): void
    {
        $smtp = optional($invoice->website)->smtp;

        if (!$this->hasUsableSmtp($smtp) && $user && $user->website_id) {
            $userWebsite = Website::with('smtp')->find($user->website_id);
            $smtp = optional($userWebsite)->smtp;
        }

        if (!$this->hasUsableSmtp($smtp)) {
            $smtp = SMTP::latest()->first();
        }

        if ($this->hasUsableSmtp($smtp)) {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $smtp->host,
                'mail.mailers.smtp.port' => $smtp->port,
                'mail.mailers.smtp.username' => $smtp->username,
                'mail.mailers.smtp.password' => $smtp->password,
                'mail.mailers.smtp.encryption' => $this->normalizeSmtpEncryption($smtp->encryption),
                'mail.from.address' => $smtp->from_email ?: config('mail.from.address'),
                'mail.from.name' => $smtp->from_name ?: config('mail.from.name'),
            ]);
        }
    }

    /**
     * Validate that the SMTP record has minimum required fields.
     */
    private function hasUsableSmtp($smtp): bool
    {
        return $smtp
            && !empty($smtp->host)
            && !empty($smtp->port)
            && !empty($smtp->username)
            && !empty($smtp->password);
    }

    /**
     * Convert legacy SMTP encryption values into valid mailer options.
     */
    private function normalizeSmtpEncryption($value): ?string
    {
        if (in_array($value, ['tls', 'ssl'], true)) {
            return $value;
        }

        if ((string) $value === '1' || $value === true) {
            return 'tls';
        }

        return null;
    }

    /**
     * Show payment page for client
     */
    public function showPayment($token)
    {
        $invoice = CustomInvoice::where('payment_token', $token)->firstOrFail();

        if ($invoice->archived_at) {
            return redirect('/')->with('error', 'This invoice is archived and no longer available for payment.');
        }

        if ($invoice->status === 'paid') {
            return redirect('/')->with('error', 'This invoice has already been paid!');
        }

        if ($invoice->status === 'expired') {
            return redirect('/')->with('error', 'This invoice has expired!');
        }

        $website = $invoice->website;

        return view('custom-invoice.pay', compact('invoice', 'website'));
    }

    /**
     * Process payment for custom invoice
     */
    public function processPayment($token, Request $request)
    {
        $invoice = CustomInvoice::where('payment_token', $token)->firstOrFail();

        if ($invoice->status === 'paid') {
            return redirect()->back()->with('error', 'This invoice has already been paid!');
        }

        $website = $invoice->website;
        $setting = Setting::find(1);

        // Determine payment amount
        $paymentType = $request->input('payment_type', 'full');
        $paymentAmount = $paymentType === 'deposit' && $invoice->refundable > 0 
            ? $invoice->refundable 
            : $invoice->total;

        $paymentMethod = $website->payment_method ?: ($setting->payment_method ?? 'authorize');

        try {
            if ($paymentMethod == 'stripe') {
                return $this->processStripePayment($invoice, $website, $setting, $request, $paymentAmount, $paymentType);
            } else {
                return $this->processAuthorizePayment($invoice, $website, $setting, $request, $paymentAmount, $paymentType);
            }
        } catch (\Throwable $e) {
            Log::error('Custom invoice payment processing exception', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Process Stripe payment
     */
    private function processStripePayment($invoice, $website, $setting, $request, $amount, $paymentType)
    {
        $secret = trim((string) ($website->stripe_secret_key ?? ''));
        if (empty($secret)) {
            $secret = trim((string) ($setting->stripe_secret ?? ''));
        }

        Stripe\Stripe::setApiKey($secret);

        try {
            $charge = Stripe\Charge::create([
                "amount" => (int) ($amount * 100),
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Custom Invoice #" . $invoice->id . " - " . ucfirst($paymentType) . " Payment",
            ]);

            $stripeCardLast4 = null;
            $stripeCardBrand = null;
            $paymentMethodDetails = $charge->payment_method_details ?? null;
            $paymentMethodCard = $paymentMethodDetails->card ?? null;
            if ($paymentMethodCard) {
                $stripeCardLast4 = trim((string) ($paymentMethodCard->last4 ?? '')) ?: null;
                $stripeCardBrand = trim((string) ($paymentMethodCard->brand ?? '')) ?: null;
            }
            if (!$stripeCardLast4 && isset($charge->source)) {
                $stripeCardLast4 = trim((string) ($charge->source->last4 ?? '')) ?: null;
                if (!$stripeCardBrand) {
                    $stripeCardBrand = trim((string) ($charge->source->brand ?? '')) ?: null;
                }
            }

            // Update invoice status based on payment type
            $status = $paymentType === 'full' ? 'paid' : 'sent'; // Partial payment keeps it as 'sent'
            
            $invoice->update([
                'status' => $status,
                'paid_at' => $paymentType === 'full' ? now() : $invoice->paid_at,
                'payment_transaction_id' => $charge->id,
            ]);

            // Create Transaction record fully aligned with main checkout system
            $transaction = $this->buildTransactionFromCustomInvoice(
                $invoice,
                $website,
                $request,
                $charge->id,
                (float) $amount,
                (string) $paymentType,
                $stripeCardLast4,
                $stripeCardBrand,
                'stripe'
            );

            $this->sendCustomInvoicePaymentConfirmation($invoice, $transaction, $website, $paymentType, $request);

            $message = $paymentType === 'deposit' 
                ? 'Deposit payment processed successfully! Remaining balance due on arrival.' 
                : 'Payment processed successfully!';

            // Redirect to thank you page with transaction details
            return redirect()->route('thank-you')
                ->with('transaction', $transaction)
                ->with('invoice', $invoice)
                ->with('website', $website)
                ->with('paymentType', $paymentType)
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process Authorize.net payment
     */
    private function processAuthorizePayment($invoice, $website, $setting, $request, $amount, $paymentType)
    {
        $clubLogin = trim((string) ($website->authorize_login_id ?? $website->authorize_app_key ?? ''));
        $clubTransKey = trim((string) ($website->authorize_transaction_key ?? $website->authorize_secret_key ?? ''));

        $globalLogin = trim((string) ($setting->authorize_login ?? $setting->authorize_key ?? ''));
        $globalTransKey = trim((string) ($setting->authorize_secret ?? ''));

        if (!empty($clubLogin) && !empty($clubTransKey)) {
            $loginId = $clubLogin;
            $transactionKey = $clubTransKey;
            $usesGlobalKeys = false;
        } else {
            $loginId = $globalLogin;
            $transactionKey = $globalTransKey;
            $usesGlobalKeys = true;
        }

        if (empty($loginId) || empty($transactionKey)) {
            return redirect()->back()->with('error', 'Payment processing failed: Authorize.Net credentials are not configured.');
        }

        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($loginId);
        $merchantAuthentication->setTransactionKey($transactionKey);

        $rawCardNumber = preg_replace('/\D/', '', (string) $request->cardNumber);
        $rawExp = preg_replace('/\D/', '', (string) $request->expirationDate);

        if (strlen($rawExp) === 4) {
            $expMonth = substr($rawExp, 0, 2);
            $expYear = '20' . substr($rawExp, 2, 2);
            $formattedExp = $expYear . '-' . $expMonth;
        } else {
            $formattedExp = $request->expirationDate;
        }

        $charge = new AnetAPI\CreditCardType();
        $charge->setCardNumber($rawCardNumber);
        $charge->setExpirationDate($formattedExp);
        $charge->setCardCode($request->cvv);

        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($charge);

        $firstName = trim((string) ($request->firstName ?? $request->billing_first_name ?? ''));
        $lastName = trim((string) ($request->lastName ?? $request->billing_last_name ?? ''));

        // Billing address for AVS
        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($firstName);
        $billTo->setLastName($lastName);
        $billTo->setAddress((string) $request->input('billing_address', ''));
        $billTo->setCity((string) $request->input('billing_city', ''));
        $billTo->setState((string) $request->input('billing_state', ''));
        $billTo->setZip((string) $request->input('billing_zip', ''));
        $billTo->setCountry((string) $request->input('billing_country', 'US'));

        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount(number_format((float) $amount, 2, '.', ''));
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($billTo);
        $transactionRequestType->setCustomerIP($request->ip());

        $request_obj = new AnetAPI\CreateTransactionRequest();
        $request_obj->setMerchantAuthentication($merchantAuthentication);
        $request_obj->setRefId('ref' . uniqid());
        $request_obj->setTransactionRequest($transactionRequestType);

        $controller = new AnetController\CreateTransactionController($request_obj);
        
        if ($usesGlobalKeys) {
            $useSandbox = $setting->sandbox_mode ?? null;
        } else {
            $useSandbox = $website->sandbox_mode ?? null;
            if ($useSandbox === null) {
                $useSandbox = $setting->sandbox_mode ?? null;
            }
        }
        if ($useSandbox === null) {
            $useSandbox = true;
        }

        $apiUrl = $useSandbox
            ? \net\authorize\api\constants\ANetEnvironment::SANDBOX 
            : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
            
        $response = $controller->executeWithApiResponse($apiUrl);

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                $rc = $tresponse != null ? (string) $tresponse->getResponseCode() : null;
                if ($tresponse != null && ($rc === '1' || $rc === '4')) {
                    $maskedAccount = trim((string) ($tresponse->getAccountNumber() ?? ''));
                    $accountDigits = preg_replace('/\D/', '', $maskedAccount);
                    $authCardLast4 = (is_string($accountDigits) && strlen($accountDigits) >= 4)
                        ? substr($accountDigits, -4)
                        : null;
                    $authCardBrand = trim((string) ($tresponse->getAccountType() ?? '')) ?: null;

                    // Update invoice status based on payment type
                    $status = $paymentType === 'full' ? 'paid' : 'sent';
                    
                    $invoice->update([
                        'status' => $status,
                        'paid_at' => $paymentType === 'full' ? now() : $invoice->paid_at,
                        'payment_transaction_id' => $tresponse->getTransId(),
                    ]);

                    // Create Transaction record fully aligned with main checkout system
                    $transaction = $this->buildTransactionFromCustomInvoice(
                        $invoice,
                        $website,
                        $request,
                        (string) $tresponse->getTransId(),
                        (float) $amount,
                        (string) $paymentType,
                        $authCardLast4,
                        $authCardBrand,
                        'authorize'
                    );

                    $this->sendCustomInvoicePaymentConfirmation($invoice, $transaction, $website, $paymentType, $request);

                    $message = $paymentType === 'deposit' 
                        ? 'Deposit payment processed successfully! Remaining balance due on arrival.' 
                        : 'Payment processed successfully!';

                    // Redirect to thank you page with transaction details
                    return redirect()->route('thank-you')
                        ->with('transaction', $transaction)
                        ->with('invoice', $invoice)
                        ->with('website', $website)
                        ->with('paymentType', $paymentType)
                        ->with('success', $message);
                }

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $error = $tresponse->getErrors()[0] ?? null;
                    $code = $error ? $error->getErrorCode() : null;
                    $text = $error ? $error->getErrorText() : 'Unknown Authorize.Net error.';
                    $errorMessage = 'Payment processing failed' . ($code ? ' (' . $code . ')' : '') . ': ' . $text;

                    Log::error('Custom invoice Authorize.Net transaction error', [
                        'invoice_id' => $invoice->id,
                        'website_id' => $website->id,
                        'sandbox_mode' => (bool) $useSandbox,
                        'error_code' => $code,
                        'error_text' => $text,
                    ]);

                    return redirect()->back()->with('error', $errorMessage);
                }
            }

            $messages = $response->getMessages()->getMessage();
            if (!empty($messages)) {
                $first = $messages[0];
                $gatewayMessage = trim(($first->getCode() ? $first->getCode() . ': ' : '') . $first->getText());

                Log::error('Custom invoice Authorize.Net API message error', [
                    'invoice_id' => $invoice->id,
                    'website_id' => $website->id,
                    'sandbox_mode' => (bool) $useSandbox,
                    'message' => $gatewayMessage,
                ]);

                return redirect()->back()->with('error', 'Payment processing failed: ' . $gatewayMessage);
            }
        }

        Log::error('Custom invoice Authorize.Net null/empty response', [
            'invoice_id' => $invoice->id,
            'website_id' => $website->id,
            'sandbox_mode' => (bool) $useSandbox,
        ]);

        return redirect()->back()->with('error', 'Payment processing failed: empty response from Authorize.Net.');
    }

    /**
     * Build and persist a complete Transaction model matching system-wide standards
     */
    private function buildTransactionFromCustomInvoice($invoice, $website, Request $request, string $transactionId, float $amount, string $paymentType, ?string $cardLast4, ?string $cardBrand, string $paymentMethod): \App\Models\Transaction
    {
        $firstName = trim((string) ($request->firstName ?? $request->billing_first_name ?? $request->cardholder_name ?? ''));
        $lastName = trim((string) ($request->lastName ?? $request->billing_last_name ?? ''));
        if (empty($firstName) && empty($lastName)) {
            $parts = explode(' ', trim((string) $invoice->client_name));
            $firstName = $parts[0] ?? $invoice->client_name;
            $lastName = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        }

        $email = trim((string) ($request->billing_email ?? $invoice->client_email));
        $phone = trim((string) ($request->billing_phone ?? ''));
        $address = trim((string) ($request->billing_address ?? ''));
        $city = trim((string) ($request->billing_city ?? ''));
        $state = trim((string) ($request->billing_state ?? ''));
        $zip = trim((string) ($request->billing_zip ?? ''));
        $country = trim((string) ($request->billing_country ?? 'US'));

        // Prepare line items JSON array for cart_items column
        $cartItems = $invoice->items->map(function ($item) {
            return [
                'name' => $item->name,
                'price' => (float) $item->price,
                'quantity' => (int) $item->quantity,
                'total' => (float) $item->getLineTotal(),
            ];
        })->toArray();

        $transaction = new \App\Models\Transaction();
        $transaction->transaction_id = $transactionId;
        $transaction->status = \App\Models\Transaction::STATUS_COMPLETED; // 1 = Completed
        $transaction->type = 'custom_invoice';
        $transaction->custom_invoice_id = $invoice->id;
        $transaction->website_id = $invoice->website_id;

        // Customer details
        $transaction->package_first_name = $firstName;
        $transaction->package_last_name = $lastName;
        $transaction->package_email = $email;
        $transaction->package_phone = $phone;

        // Payment & Billing details
        $transaction->payment_first_name = $firstName;
        $transaction->payment_last_name = $lastName;
        $transaction->payment_email = $email;
        $transaction->payment_phone = $phone;
        $transaction->payment_address = $address;
        $transaction->payment_city = $city;
        $transaction->payment_state = $state;
        $transaction->payment_zip_code = $zip;
        $transaction->payment_country = $country;
        $transaction->payment_method = $paymentMethod;
        $transaction->payment_card_last4 = $cardLast4;
        $transaction->payment_card_brand = $cardBrand;

        // Financial totals
        $transaction->sub_total = (float) $invoice->subtotal;
        $transaction->sales_tax = (float) $invoice->sales_tax;
        $transaction->service_charge = (float) $invoice->service_charge;
        $transaction->gratuity = (float) $invoice->gratuity;
        $transaction->processing_fee = (float) $invoice->processing_fee;
        $transaction->refundable = (float) $invoice->refundable;
        $transaction->total = $amount;
        $transaction->actual_total = (float) $invoice->total;
        $transaction->due = max((float) $invoice->total - $amount, 0);

        // Cart items & Ticket QR Code
        $transaction->cart_items = $cartItems;
        $transaction->ticket_qr_code = 'INV-' . $invoice->id . '-' . strtoupper(substr(md5($transactionId), 0, 8));
        $transaction->ip_address = $request->ip();

        // Connect Creator / Promoter / Sub-promoter / Entertainer / Staff
        $creator = $invoice->user;
        if ($creator) {
            $transaction->user_id = $creator->id;

            // 1. Check Affiliate (Promoter / Sub-promoter)
            $affiliate = $creator->affiliate;
            if (!$affiliate) {
                $affiliate = \App\Models\Affiliate::where('user_id', $creator->id)->first();
            }

            if ($affiliate) {
                $transaction->affiliate_id = $affiliate->id;
                $transaction->promoter_id = $affiliate->parent_id ?: $affiliate->id;

                $affWeb = \App\Models\AffiliateWebsite::where('affiliate_id', $affiliate->id)
                    ->where('website_id', $invoice->website_id)
                    ->first();

                $commRate = $affWeb ? (float) $affWeb->commission_percentage : 0;
                if ($commRate <= 0) {
                    $commRate = (float) ($affiliate->default_commission_percentage ?? 0);
                }

                if ($commRate > 0) {
                    $commAmount = round($amount * ($commRate / 100), 2);
                    $transaction->affiliate_commission_percentage = $commRate;
                    $transaction->affiliate_commission_amount = $commAmount;
                    $transaction->affiliate_commission_status = \App\Models\Transaction::COMMISSION_STATUS_PENDING;

                    $holdDays = (int) ($website->commission_hold_days ?? 0);
                    if ($holdDays > 0) {
                        $transaction->affiliate_commission_hold_until = now()->addDays($holdDays);
                    }
                }
            }

            // 2. Check Entertainer
            $entertainer = $creator->entertainer;
            if (!$entertainer) {
                $entertainer = \App\Models\Entertainer::where('user_id', $creator->id)->first();
            }

            if ($entertainer) {
                $transaction->entertainer_id = $entertainer->id;

                $entRate = (float) ($entertainer->default_commission_percentage ?? 0);
                if ($entRate > 0) {
                    $entAmount = round($amount * ($entRate / 100), 2);
                    $transaction->entertainer_commission_percentage = $entRate;
                    $transaction->entertainer_commission_amount = $entAmount;
                    $transaction->entertainer_commission_status = \App\Models\Transaction::COMMISSION_STATUS_PENDING;

                    $holdDays = (int) ($website->commission_hold_days ?? 0);
                    if ($holdDays > 0) {
                        $transaction->entertainer_commission_hold_until = now()->addDays($holdDays);
                    }
                }
            }
        }

        $noteContent = !empty($invoice->internal_notes) ? $invoice->internal_notes : $invoice->notes;
        if (!empty($noteContent)) {
            $transaction->package_note = $noteContent;
            $transaction->admin_notes = $noteContent;
            $transaction->admin_notes_by = auth()->user()->name ?? 'System (Custom Invoice)';
            $transaction->admin_notes_at = now();
        }

        $transaction->save();

        return $transaction;
    }

    private function sendCustomInvoicePaymentConfirmation($invoice, $transaction, $website, string $paymentType, Request $request): void
    {
        try {
            $this->applyInvoiceSmtpConfig($invoice, auth()->user() ?? $invoice->user);

            $clientMail = new CustomInvoicePaymentConfirmationMail(
                $invoice,
                $transaction,
                $paymentType,
                $website,
                (string) ($request->cardholder_name ?? $request->firstName ?? $transaction->payment_first_name ?? ''),
                (string) ($request->lastName ?? $transaction->payment_last_name ?? '')
            );

            $managerMail = (clone $clientMail)->subject(
                'Custom Invoice Payment Confirmation - ' . $transaction->transaction_id . ' - ' . ($website->name ?? 'Club')
            );

            if ($invoice->client_email && filter_var($invoice->client_email, FILTER_VALIDATE_EMAIL)) {
                Mail::to($invoice->client_email)->send(clone $clientMail);
            }

            // Send official Transaction receipt with PDF attachment and Ticket QR Code
            try {
                $mailData = [
                    'transaction_id' => $transaction->transaction_id,
                    'order_id' => $transaction->id,
                    'website_name' => $website->name ?? 'Venue',
                    'club_name' => $website->name ?? 'Venue',
                    'package_first_name' => $transaction->package_first_name,
                    'package_last_name' => $transaction->package_last_name,
                    'package_email' => $transaction->package_email,
                    'package_phone' => $transaction->package_phone,
                    'type' => 'custom_invoice',
                    'cart_items' => $transaction->cart_items,
                    'price_breakdown' => [
                        'sub_total' => $transaction->sub_total,
                        'sales_tax' => $transaction->sales_tax,
                        'service_charge' => $transaction->service_charge,
                        'gratuity' => $transaction->gratuity,
                        'processing_fee' => $transaction->processing_fee,
                        'total' => $transaction->total,
                    ],
                ];

                $txnMail = new TransactionMail($mailData, $transaction, $transaction->cart_items, $mailData['price_breakdown'], $website, true, 'guest');
                Mail::to($transaction->package_email)->send($txnMail);
            } catch (\Throwable $txnMailEx) {
                Log::warning('Custom invoice TransactionMail dispatch failed', ['error' => $txnMailEx->getMessage()]);
            }

            // Dispatch Telnyx SMS confirmation if configured
            if (!empty($transaction->package_phone)) {
                try {
                    $telnyx = new \App\Services\TelnyxSmsService();
                    if ($telnyx->isConfigured()) {
                        $msg = "Payment Confirmed: Thank you for your payment of $" . number_format($transaction->total, 2) . " to " . ($website->name ?? 'Venue') . ". Confirmation #: " . $transaction->transaction_id . ". View ticket: " . url('/custom-invoice/' . $invoice->payment_token . '/pay');
                        $telnyx->sendSms($transaction->package_phone, $msg);
                    }
                } catch (\Throwable $smsEx) {
                    Log::warning('Custom invoice payment SMS failed', ['error' => $smsEx->getMessage()]);
                }
            }

            $managerEmails = collect($website->emails ?? [])->pluck('email')
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->unique()
                ->values();

            foreach ($managerEmails as $managerEmail) {
                Mail::to($managerEmail)->send(clone $managerMail);
            }
        } catch (\Throwable $e) {
            Log::warning('Custom invoice payment confirmation email failed', [
                'invoice_id' => $invoice->id,
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
