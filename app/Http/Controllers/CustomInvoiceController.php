<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomInvoice;
use App\Models\CustomInvoiceItem;
use App\Models\Website;
use App\Models\Setting;
use App\Mail\CustomInvoiceMail;
use Illuminate\Support\Facades\Mail;
use Stripe;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class CustomInvoiceController extends Controller
{
    /**
     * Display a listing of custom invoices
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $invoices = CustomInvoice::with(['website', 'items'])->latest()->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            $invoices = CustomInvoice::where('website_id', $user->website_id)
                                    ->with(['items'])
                                    ->latest()
                                    ->get();
        } else {
            $invoices = collect();
        }

        return view('admin.custom-invoice.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new custom invoice
     */
    public function create()
    {
        $user = auth()->user();
        
        if ($user->isWebsiteUser()) {
            $websites = Website::where('id', $user->website_id)->get();
        } else {
            $websites = Website::all();
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
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        $invoice = new CustomInvoice();
        $invoice->user_id = $user->id;
        $invoice->website_id = $request->website_id;
        $invoice->client_name = $request->client_name;
        $invoice->client_email = $request->client_email;
        $invoice->notes = $request->notes;
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

        // Check if we should send immediately
        if ($request->input('action') === 'send') {
            try {
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
        
        if ($user->isWebsiteUser() && $customInvoice->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        return view('admin.custom-invoice.show', compact('customInvoice'));
    }

    /**
     * Show the form for editing the specified custom invoice
     */
    public function edit(CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        if ($user->isWebsiteUser() && $customInvoice->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only edit draft invoices!');
        }

        $websites = $user->isWebsiteUser() 
                    ? Website::where('id', $user->website_id)->get() 
                    : Website::all();

        return view('admin.custom-invoice.edit', compact('customInvoice', 'websites'));
    }

    /**
     * Update the specified custom invoice
     */
    public function update(Request $request, CustomInvoice $customInvoice)
    {
        $user = auth()->user();
        
        if ($user->isWebsiteUser() && $customInvoice->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only edit draft invoices!');
        }

        $request->validate([
            'website_id' => 'required|exists:websites,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0.01',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $customInvoice->update([
            'client_name' => $request->client_name,
            'client_email' => $request->client_email,
            'notes' => $request->notes,
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
        
        if ($user->isWebsiteUser() && $customInvoice->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        if ($customInvoice->status !== 'draft') {
            return redirect()->back()->with('error', 'Can only send draft invoices!');
        }

        try {
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
        
        if ($user->isWebsiteUser() && $customInvoice->website_id != $user->website_id) {
            abort(403, 'Unauthorized');
        }

        if ($customInvoice->status === 'paid') {
            return redirect()->back()->with('error', 'Cannot delete paid invoices!');
        }

        $customInvoice->delete();
        return redirect()->route('admin.custom-invoice.index')->with('success', 'Invoice deleted successfully!');
    }

    /**
     * Show payment page for client
     */
    public function showPayment($token)
    {
        $invoice = CustomInvoice::where('payment_token', $token)->firstOrFail();

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

        try {
            if ($website->payment_method == 'stripe') {
                return $this->processStripePayment($invoice, $website, $setting, $request, $paymentAmount, $paymentType);
            } elseif ($website->payment_method == 'authorize') {
                return $this->processAuthorizePayment($invoice, $website, $setting, $request, $paymentAmount, $paymentType);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process Stripe payment
     */
    private function processStripePayment($invoice, $website, $setting, $request, $amount, $paymentType)
    {
        $secret = $website->stripe_secret_key ?? $setting->stripe_secret;
        Stripe\Stripe::setApiKey($secret);

        try {
            $charge = Stripe\Charge::create([
                "amount" => (int) ($amount * 100),
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Custom Invoice #" . $invoice->id . " - " . ucfirst($paymentType) . " Payment",
            ]);

            // Update invoice status based on payment type
            $status = $paymentType === 'full' ? 'paid' : 'sent'; // Partial payment keeps it as 'sent'
            
            $invoice->update([
                'status' => $status,
                'paid_at' => $paymentType === 'full' ? now() : $invoice->paid_at,
                'payment_transaction_id' => $charge->id,
            ]);

            // Create Transaction record for tracking
            $transaction = new \App\Models\Transaction();
            $transaction->transaction_id = $charge->id;
            $transaction->package_first_name = $invoice->client_name;
            $transaction->package_email = $invoice->client_email;
            $transaction->payment_first_name = $request->cardholder_name ?? $invoice->client_name;
            $transaction->payment_email = $invoice->client_email;
            $transaction->event_id = null;
            $transaction->website_id = $invoice->website_id;
            $transaction->total = $amount;
            $transaction->actual_total = $amount;
            $transaction->type = 'custom_invoice';
            $transaction->custom_invoice_id = $invoice->id;
            $transaction->ip_address = $request->ip();
            $transaction->save();

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
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($website->authorize_login_id ?? $setting->authorize_login);
        $merchantAuthentication->setTransactionKey($website->authorize_transaction_key ?? $setting->authorize_key);

        $charge = new AnetAPI\CreditCardType();
        $charge->setCardNumber($request->cardNumber);
        $charge->setExpirationDate($request->expirationDate);
        $charge->setCardCode($request->cvv);

        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($charge);

        $billTo = new AnetAPI\CustomerAddressType();
        $billTo->setFirstName($request->firstName ?? '');
        $billTo->setLastName($request->lastName ?? '');

        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($billTo);

        $request_obj = new AnetAPI\CreateTransactionRequest();
        $request_obj->setMerchantAuthentication($merchantAuthentication);
        $request_obj->setRefId(uniqid());
        $request_obj->setTransactionRequest($transactionRequestType);

        $controller = new AnetController\CreateTransactionController($request_obj);
        
        $apiUrl = ($website->sandbox_mode ?? $setting->sandbox_mode) 
            ? \net\authorize\api\constants\AnetEnvironment::SANDBOX 
            : \net\authorize\api\constants\AnetEnvironment::PRODUCTION;
            
        $response = $controller->executeWithApiResponse($apiUrl);

        if ($response != null) {
            if ($response->getMessages()->getResultCode() == "Ok") {
                $tresponse = $response->getTransactionResponse();
                if ($tresponse != null && $tresponse->getMessages() != null) {
                    // Update invoice status based on payment type
                    $status = $paymentType === 'full' ? 'paid' : 'sent';
                    
                    $invoice->update([
                        'status' => $status,
                        'paid_at' => $paymentType === 'full' ? now() : $invoice->paid_at,
                        'payment_transaction_id' => $tresponse->getTransId(),
                    ]);

                    // Create Transaction record for tracking
                    $transaction = new \App\Models\Transaction();
                    $transaction->transaction_id = $tresponse->getTransId();
                    $transaction->package_first_name = $invoice->client_name;
                    $transaction->package_email = $invoice->client_email;
                    $transaction->payment_first_name = $request->firstName . ' ' . $request->lastName;
                    $transaction->payment_email = $invoice->client_email;
                    $transaction->event_id = null;
                    $transaction->website_id = $invoice->website_id;
                    $transaction->total = $amount;
                    $transaction->actual_total = $amount;
                    $transaction->type = 'custom_invoice';
                    $transaction->custom_invoice_id = $invoice->id;
                    $transaction->ip_address = $request->ip();
                    $transaction->save();

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
            }
        }

        return redirect()->back()->with('error', 'Payment processing failed!');
    }
}
