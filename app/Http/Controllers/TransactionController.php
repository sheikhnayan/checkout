<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Event;
use App\Models\Package;
use App\Models\Website;
use App\Models\Setting;
use App\Models\Affiliate;
use App\Models\AffiliatePackage;
use App\Models\AffiliateWebsite;
use App\Models\AffiliateWalletTransaction;
use App\Models\Entertainer;
use App\Models\EntertainerPackage;
use App\Models\EntertainerWalletTransaction;
use App\Models\PromoCode;
use App\Models\Addon;
use App\Services\CommissionLifecycleRunner;
use App\Mail\TransactionMail;
use App\Helpers\PackageLimitHelper;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Stripe;

class TransactionController extends Controller
{
    public function store($slug, Request $request)
    {
        // dd($request->all());

        $cartItems = $this->extractCartItemsFromRequest($request);
        $cartSummary = $this->summarizeCartItems($cartItems);
        $requestedUseDate = null;
        if ($request->filled('package_use_date')) {
            try {
                $requestedUseDate = Carbon::parse((string) $request->input('package_use_date'))->startOfDay();
            } catch (\Throwable $exception) {
                $requestedUseDate = null;
            }
        }

        $selectedPackage = Package::find($cartSummary['primary_package_id'] ?: $request->input('package_id'));
        $requiresTransportation = $this->cartRequiresTransportation($cartItems, $selectedPackage);

        $this->ensureCartEventCapacitiesAvailable($cartItems, $requestedUseDate);

        // Validate daily package limits
        foreach ($cartItems as $item) {
            $package = Package::find($item['package_id']);
            if ($package) {
                $requestedQuantity = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
                $result = PackageLimitHelper::canPurchase($package, $requestedQuantity, $requestedUseDate);
                if (!$result['allowed']) {
                    throw ValidationException::withMessages(['package_limit' => $result['message']]);
                }
            }
        }

        if ($requiresTransportation) {
            $request->validate(
                [
                    'package_use_date' => ['required', 'date'],
                    'transportation_pickup_time' => ['required', 'string', 'max:100'],
                    'transportation_address' => ['required', 'string', 'max:255'],
                    'transportation_phone' => ['required', 'string', 'max:50'],
                ],
                [
                    'package_use_date.required' => 'Pickup date is required for transportation packages.',
                    'package_use_date.date' => 'Pickup date must be a valid date.',
                    'transportation_pickup_time.required' => 'Pickup time is required for transportation packages.',
                    'transportation_address.required' => 'Pickup location is required for transportation packages.',
                    'transportation_phone.required' => 'Contact Phone Number or WhatsApp is required for transportation packages.',
                ]
            );

            $scheduleWebsite = Website::find($request->website_id);
            if ($scheduleWebsite) {
                $this->validateTransportationAvailability($scheduleWebsite, $request, $selectedPackage);
            }
        }

        $setting = Setting::find(1);

        $w = Website::find($request->website_id);
        [$validatedPromoCodeId, $validatedDiscountAmount] = $this->resolveValidatedPromoForCheckout($request, $w);

        if ($w->payment_method == 'stripe') {
            # code...

            $w = Website::find($request->website_id);

            if ($w->stripe_secret_key != null) {
                # code...
                $secret = $w->stripe_secret_key;
            }else{
                $secret = $setting->stripe_secret;
            }

            Stripe\Stripe::setApiKey($secret);

                        // 3️⃣ Create a one‑time token from the raw card data
                        $charge = Stripe\Charge::create ([
                                "amount" => $request->total * 100,
                                "currency" => "usd",
                                "source" => $request->stripeToken,
                                "description" => "Payment fit"
                        ]);

    
                    $transaction_id = $charge->id;
    
                    $ipAddress = $request->ip();
    
                    $add = new Transaction();
                    $add->transaction_id = $transaction_id;
                    $add->ticket_qr_code = $this->generateTicketQrCode();
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $cartSummary['total_guests'];
                    $add->package_use_date = $request->input('package_use_date');
                    $add->business_company = $request->business_company;
                    $add->business_vat = $request->business_vat;
                    $add->business_address = $request->business_address;
                    $add->business_purpose = $request->business_purpose;
                    // Merge package DOB
                    $package_month = $request->input('package_month');
                    $package_day = $request->input('package_day');
                    $package_year = $request->input('package_year');
                    $add->package_dob = ($package_year && $package_month && $package_day) ? (sprintf('%04d-%02d-%02d', $package_year, $package_month, $package_day)) : null;
                    $add->package_note = $request->input('package_note');
                    $add->host_name = $request->input('host_name');
                    $add->promo_code = $validatedPromoCodeId;
                    $add->actual_total = $request->input('payment_total');
                    $add->discounted_amount = $validatedDiscountAmount;
                    $add->transportation_pickup_time = $request->input('transportation_pickup_time');
                    $add->transportation_address = $request->input('transportation_address');
                    $add->transportation_phone = $request->input('transportation_phone');
                    $add->transportation_guest = $request->input('transportation_guest');
                    $add->transportation_note = $request->input('transportation_note');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->package_id = $cartSummary['primary_package_id'] ?: $request->input('package_id');
                    $add->cart_items = !empty($cartItems) ? $cartItems : null;
                    $add->payment_first_name = $request->input('payment_first_name');
                    $add->payment_last_name = $request->input('payment_last_name');
                    $add->payment_phone = $request->input('payment_phone');
                    $add->payment_email = $request->input('payment_email');
                    $add->payment_address = $request->input('payment_address');
                    $add->payment_city = $request->input('payment_city');
                    $add->payment_state = $request->input('payment_state');
                    $add->payment_country = $request->input('payment_country');
                    // Merge payment DOB
                    $payment_month = $request->input('payment_month');
                    $payment_day = $request->input('payment_day');
                    $payment_year = $request->input('payment_year');
                    $add->payment_dob = ($payment_year && $payment_month && $payment_day) ? (sprintf('%04d-%02d-%02d', $payment_year, $payment_month, $payment_day)) : null;
                    $add->payment_zip_code = $request->input('payment_zip_code');
    
    
                    $event_id = optional($selectedPackage)->event_id;
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->type = 'package';
                    $add->save();
                    $this->incrementPromoUsage($validatedPromoCodeId);
                    $this->applyReferralCommission($request, $add, (float) ($cartSummary['commission_base_amount'] ?? 0));
    
                    try {
                        //code...
                        // Prepare all transaction data for the email body
                        $mailData = [
                            'transaction_id' => $transaction_id,
                            'package_first_name' => $request->input('package_first_name'),
                            'package_last_name' => $request->input('package_last_name'),
                            'package_phone' => $request->input('package_phone'),
                            'package_email' => $request->input('package_email'),
                            'package_use_date' => $request->input('package_use_date'),
                            'package_dob' => $add->package_dob,
                            'package_note' => $request->input('package_note'),
                            'transportation_pickup_time' => $request->input('transportation_pickup_time'),
                            'transportation_address' => $request->input('transportation_address'),
                            'transportation_phone' => $request->input('transportation_phone'),
                            'transportation_guest' => $request->input('transportation_guest'),
                            'transportation_note' => $request->input('transportation_note'),
                            'business_company' => $add->business_company,
                            'business_vat' => $add->business_vat,
                            'business_address' => $add->business_address,
                            'addons' => $cartSummary['addons_summary'],
                            'package_id' => $cartSummary['primary_package_id'] ?: $request->input('package_id'),
                            'cart_items' => $cartItems,
                            'payment_first_name' => $request->input('payment_first_name'),
                            'payment_last_name' => $request->input('payment_last_name'),
                            'payment_phone' => $request->input('payment_phone'),
                            'payment_email' => $request->input('payment_email'),
                            'payment_address' => $request->input('payment_address'),
                            'payment_city' => $request->input('payment_city'),
                            'payment_state' => $request->input('payment_state'),
                            'payment_country' => $request->input('payment_country'),
                            'payment_dob' => $add->payment_dob,
                            'payment_zip_code' => $request->input('payment_zip_code'),
                            'event_id' => $event_id,
                            'website_id' => $website_id,
                            'total' => $request->input('total'),
                            'type' => 'package',
                            'ticket_qr_code' => $add->ticket_qr_code,
                            'ticket_qr_image_url' => $this->buildTicketQrImageUrl($add->ticket_qr_code),
                        ];
    
                        $website = Website::findOrFail($website_id);
                        $mailData['club_name'] = $website->name;
                        $mailData['website_name'] = $website->name;
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr, $add, $cartItems, $mailData['price_breakdown'], $website, false, 'manager');

                        // Purchaser email — full mail with QR
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailData, $add, $cartItems, $mailData['price_breakdown'], $website, true, 'guest');

                        $clubEmails = collect($website->emails ?? [])
                            ->pluck('email')
                            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                            ->push('hello@cartvip.com')
                            ->unique()
                            ->values();

                        foreach ($clubEmails as $clubEmail) {
                            \Illuminate\Support\Facades\Mail::to($clubEmail)->send(clone $send_mail_club);
                        }

                        $purchaserEmail = $request->input('package_email');
                        if ($purchaserEmail && filter_var($purchaserEmail, FILTER_VALIDATE_EMAIL)) {
                            \Illuminate\Support\Facades\Mail::to($purchaserEmail)->send($send_mail_purchaser);
                        }

                        // ========== SEND SMS NOTIFICATION ==========
                        $purchaserPhone = $request->input('package_phone');
                        if ($purchaserPhone) {
                            try {
                                \Log::info('Attempting to send package SMS', ['phone' => $purchaserPhone]);
                                $smsService = new \App\Services\TelnyxSmsService();
                                $packageData = $this->getPackageData($slug, $request);
                                $smsData = [
                                    'transaction_id' => $add->transaction_id,
                                    'club_name' => $website->name ?? 'Venue',
                                    'club_slug' => $website->slug ?? '',
                                    'package_name' => $packageData['name'] ?? 'Package',
                                    'quantity' => $request->input('quantity', 1),
                                    'package_use_date' => $request->input('package_use_date') ?? '',
                                    'total_amount' => $add->total,
                                ];
                                $result = $smsService->sendTransactionNotification($purchaserPhone, $smsData, 'package');
                                \Log::info('SMS result for package', ['result' => $result]);
                            } catch (\Exception $smsError) {
                                \Log::error('SMS notification failed for package: ' . $smsError->getMessage(), ['trace' => $smsError->getTraceAsString()]);
                            }
                        } else {
                            \Log::warning('No phone number provided for package SMS');
                        }
                    } catch (\Throwable $th) {
                        report($th);
                        throw ValidationException::withMessages([
                            'email' => 'Email delivery failed: ' . $th->getMessage(),
                        ]);
                    }




                    // Redirect to thank you page with transaction details
                    return redirect()->route('thank-you')
                        ->with('transaction', $add->fresh())
                        ->with('website', $website)
                        ->with('paymentType', 'full');



        } else {
            # code...
            $cardNumber = $request->input('card_number');
            $date = \Carbon\Carbon::parse($request->input('card_month').'/'.$request->input('card_year'))->format('m/y');
            // dd($date);
            $expirationDate = $date;
            $cvv = $request->input('card_cvv');

            $w = Website::find($request->website_id);

            if ($w->authorize_app_key != null) {
                # code...
                $app = $w->authorize_app_key;
                $secret = $w->authorize_secret_key;
            } else {
                # code...
                $app = $setting->authorize_key;
                $secret = $setting->authorize_secret;
            }
            

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($app);
            $merchantAuthentication->setTransactionKey($secret);

            $creditCard = new AnetAPI\CreditCardType();
            $creditCard->setCardNumber($cardNumber);
            $creditCard->setExpirationDate($expirationDate);
            $creditCard->setCardCode($cvv);
    
            $payment = new AnetAPI\PaymentType();
            $payment->setCreditCard($creditCard);
    
            $transactionRequestType = new AnetAPI\TransactionRequestType();
    
            $transactionRequestType->setTransactionType("authCaptureTransaction");
    
            $amount = number_format((float)$request->total, 2, '.', '');
            $transactionRequestType->setAmount($amount);
            // $transactionRequestType->setAmount("10.00");
            $transactionRequestType->setPayment($payment);
    
            $requests = new AnetAPI\CreateTransactionRequest();
            $requests->setMerchantAuthentication($merchantAuthentication);
            $requests->setRefId("ref" . time());
            $requests->setTransactionRequest($transactionRequestType);
    
            $controller = new AnetController\CreateTransactionController($requests);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
            if ($response != null) {
                $tresponse = $response->getTransactionResponse();
                // dd($response);
                if ($tresponse != null & $tresponse->getResponseCode() == "1") {
                    # code...
                    $tresponse = $response->getTransactionResponse();
    
                    $transaction_id = $tresponse->getTransId();
    
                    $ipAddress = $request->ip();
    
                    $add = new Transaction();
                    $add->transaction_id = $transaction_id;
                    $add->ticket_qr_code = $this->generateTicketQrCode();
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $cartSummary['total_guests'];
                    $add->package_use_date = $request->input('package_use_date');
                    $add->business_company = $request->business_company;
                    $add->business_vat = $request->business_vat;
                    $add->business_address = $request->business_address;
                    $add->business_purpose = $request->business_purpose;
                    // Merge package DOB
                    $package_month = $request->input('package_month');
                    $package_day = $request->input('package_day');
                    $package_year = $request->input('package_year');
                    $add->package_dob = ($package_year && $package_month && $package_day) ? (sprintf('%04d-%02d-%02d', $package_year, $package_month, $package_day)) : null;
                    $add->package_note = $request->input('package_note');
                    $add->host_name = $request->input('host_name');
                    $add->promo_code = $validatedPromoCodeId;
                    $add->actual_total = $request->input('payment_total');
                    $add->discounted_amount = $validatedDiscountAmount;
                    $add->transportation_pickup_time = $request->input('transportation_pickup_time');
                    $add->transportation_address = $request->input('transportation_address');
                    $add->transportation_phone = $request->input('transportation_phone');
                    $add->transportation_guest = $request->input('transportation_guest');
                    $add->transportation_note = $request->input('transportation_note');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->package_id = $cartSummary['primary_package_id'] ?: $request->input('package_id');
                    $add->cart_items = !empty($cartItems) ? $cartItems : null;
                    $add->payment_first_name = $request->input('payment_first_name');
                    $add->payment_last_name = $request->input('payment_last_name');
                    $add->payment_phone = $request->input('payment_phone');
                    $add->payment_email = $request->input('payment_email');
                    $add->payment_address = $request->input('payment_address');
                    $add->payment_city = $request->input('payment_city');
                    $add->payment_state = $request->input('payment_state');
                    $add->payment_country = $request->input('payment_country');
                    // Merge payment DOB
                    $payment_month = $request->input('payment_month');
                    $payment_day = $request->input('payment_day');
                    $payment_year = $request->input('payment_year');
                    $add->payment_dob = ($payment_year && $payment_month && $payment_day) ? (sprintf('%04d-%02d-%02d', $payment_year, $payment_month, $payment_day)) : null;
                    $add->payment_zip_code = $request->input('payment_zip_code');
    
    
                    $event_id = optional($selectedPackage)->event_id;
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->type = 'package';
                    $add->save();
                    $this->incrementPromoUsage($validatedPromoCodeId);
                    $this->applyReferralCommission($request, $add, (float) ($cartSummary['commission_base_amount'] ?? 0));
    
                    try {
                        //code...
                        // Prepare all transaction data for the email body
                        $mailData = [
                            'transaction_id' => $transaction_id,
                            'package_first_name' => $request->input('package_first_name'),
                            'package_last_name' => $request->input('package_last_name'),
                            'package_phone' => $request->input('package_phone'),
                            'package_email' => $request->input('package_email'),
                            'package_use_date' => $request->input('package_use_date'),
                            'package_dob' => $add->package_dob,
                            'package_note' => $request->input('package_note'),
                            'transportation_pickup_time' => $request->input('transportation_pickup_time'),
                            'transportation_address' => $request->input('transportation_address'),
                            'transportation_phone' => $request->input('transportation_phone'),
                            'transportation_guest' => $request->input('transportation_guest'),
                            'transportation_note' => $request->input('transportation_note'),
                            'business_company' => $add->business_company,
                            'business_vat' => $add->business_vat,
                            'business_address' => $add->business_address,
                            'addons' => $cartSummary['addons_summary'],
                            'package_id' => $cartSummary['primary_package_id'] ?: $request->input('package_id'),
                            'cart_items' => $cartItems,
                            'payment_first_name' => $request->input('payment_first_name'),
                            'payment_last_name' => $request->input('payment_last_name'),
                            'payment_phone' => $request->input('payment_phone'),
                            'payment_email' => $request->input('payment_email'),
                            'payment_address' => $request->input('payment_address'),
                            'payment_city' => $request->input('payment_city'),
                            'payment_state' => $request->input('payment_state'),
                            'payment_country' => $request->input('payment_country'),
                            'payment_dob' => $add->payment_dob,
                            'payment_zip_code' => $request->input('payment_zip_code'),
                            'event_id' => $event_id,
                            'website_id' => $website_id,
                            'total' => $request->input('total'),
                            'type' => 'package',
                            'ticket_qr_code' => $add->ticket_qr_code,
                            'ticket_qr_image_url' => $this->buildTicketQrImageUrl($add->ticket_qr_code),
                        ];
    
                        $website = Website::findOrFail($website_id);
                        $mailData['club_name'] = $website->name;
                        $mailData['website_name'] = $website->name;
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr, $add, $cartItems, $mailData['price_breakdown'], $website, false, 'manager');

                        // Purchaser email — full mail with QR
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailData, $add, $cartItems, $mailData['price_breakdown'], $website, true, 'guest');

                        $clubEmails = collect($website->emails ?? [])
                            ->pluck('email')
                            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                            ->push('hello@cartvip.com')
                            ->unique()
                            ->values();

                        foreach ($clubEmails as $clubEmail) {
                            \Illuminate\Support\Facades\Mail::to($clubEmail)->send(clone $send_mail_club);
                        }

                        $purchaserEmail = $request->input('package_email');
                        if ($purchaserEmail && filter_var($purchaserEmail, FILTER_VALIDATE_EMAIL)) {
                            \Illuminate\Support\Facades\Mail::to($purchaserEmail)->send($send_mail_purchaser);
                        }
                    } catch (\Throwable $th) {
                        report($th);
                        throw ValidationException::withMessages([
                            'email' => 'Email delivery failed: ' . $th->getMessage(),
                        ]);
                    }
    
    
    
    
                    // Redirect to thank you page with transaction details
                    return redirect()->route('thank-you')
                        ->with('transaction', $add->fresh())
                        ->with('website', $website)
                        ->with('paymentType', 'full');
                }else{
                    return back()->with('error', "Payment failed: ". $response->getMessages()->getMessage()[0]->getText());
                }
            }else{
                return back()->with('error', "Payment failed: ". $response->getMessages()->getMessage()[0]->getText());
            }
        }
        


        // dd($request->all()); // This line is for debugging purposes, you can remove it later
    }

    public function test()
    {
        // Minimal test: send to a hardcoded address using default SMTP config
        // dd('Test method called. About to send test mail...');
        try {
            $mailData = [ 'type' => 'package' ];
            $send_mail = new \App\Mail\TransactionMail($mailData);
            $send_mail->subject('Test Mail - ' . now());
            $to = 'nman0171@gmail.com'; // <-- change to your real email for testing
            Mail::to($to)->send($send_mail);
            dd('Test mail sent to ' . $to);
        } catch (\Throwable $th) {
            dd('Exception: ' . $th->getMessage());
        }
    }

    public function index(Request $request)
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $data = $this->getAccessibleTransactionList($request);

        return view('admin.transaction.index', [
            'data' => $data,
            'dashboardTitle' => 'Transactions Dashboard',
            'dashboardSubtitle' => "Here's what's happening with your transaction performance.",
        ]);
    }

    public function affiliateIndex(Request $request)
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $data = $this->getAccessibleTransactionList($request, function ($query) {
            $query->whereNotNull('affiliate_id');
        });

        return view('admin.transaction.index', [
            'data' => $data,
            'dashboardTitle' => 'affiliate Transactions',
            'dashboardSubtitle' => 'Only affiliate-referred transactions are listed here.',
            'isPayoutPage' => true,
        ]);
    }

    public function entertainerIndex(Request $request)
    {
        app(CommissionLifecycleRunner::class)->runSafely();

        $data = $this->getAccessibleTransactionList($request, function ($query) {
            $query->whereNotNull('entertainer_id');
        });

        return view('admin.transaction.index', [
            'data' => $data,
            'dashboardTitle' => 'Entertainer Transactions',
            'dashboardSubtitle' => 'Only entertainer-referred transactions are listed here.',
            'isPayoutPage' => true,
        ]);
    }

    private function getAccessibleTransactionList(Request $request, ?callable $queryMutator = null)
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $query = Transaction::query();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            $query = Transaction::query()->where(function($query) use ($user) {
                $query->where('website_id', $user->website_id)
                    ->orWhereHas('event', function($subQuery) use ($user) {
                        $subQuery->where('website_id', $user->website_id);
                    })
                    ->orWhereHas('package', function($subQuery) use ($user) {
                        $subQuery->where('website_id', $user->website_id);
                    });
            });
        } else {
            return collect();
        }

        if ($queryMutator) {
            $queryMutator($query);
        }

        $websiteName = trim((string) $request->query('website', ''));
        if ($websiteName !== '') {
            $query->whereHas('website', function ($websiteQuery) use ($websiteName) {
                $websiteQuery->where('name', $websiteName);
            });
        }

        $type = strtolower(trim((string) $request->query('type', '')));
        if ($type === 'package' || $type === 'reservation') {
            $query->where('type', $type);
        }

        $statusMap = [
            'completed' => 1,
            'canceled' => 0,
            'refunded' => 2,
        ];
        $statusKey = strtolower(trim((string) $request->query('status', '')));
        if (array_key_exists($statusKey, $statusMap)) {
            $query->where('status', $statusMap[$statusKey]);
        }

        $affiliateFilter = trim((string) $request->query('affiliate', ''));
        if ($affiliateFilter !== '') {
            if (strcasecmp($affiliateFilter, 'Direct') === 0) {
                $query->whereNull('affiliate_id')->whereNull('entertainer_id');
            } else {
                $query->where(function ($nameQuery) use ($affiliateFilter) {
                    $nameQuery->whereHas('affiliate', function ($affiliateQuery) use ($affiliateFilter) {
                        $affiliateQuery->where('display_name', $affiliateFilter)
                            ->orWhereHas('user', function ($userQuery) use ($affiliateFilter) {
                                $userQuery->where('name', $affiliateFilter);
                            });
                    })->orWhereHas('entertainer', function ($entertainerQuery) use ($affiliateFilter) {
                        $entertainerQuery->where('display_name', $affiliateFilter)
                            ->orWhereHas('user', function ($userQuery) use ($affiliateFilter) {
                                $userQuery->where('name', $affiliateFilter);
                            });
                    });
                });
            }
        }

        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));
        if ($dateFrom !== '' && $dateTo !== '') {
            try {
                $startUtc = Carbon::parse($dateFrom, 'America/Los_Angeles')->startOfDay()->utc();
                $endUtc = Carbon::parse($dateTo, 'America/Los_Angeles')->endOfDay()->utc();
                $query->whereBetween('created_at', [$startUtc, $endUtc]);
            } catch (\Throwable $exception) {
                // Ignore malformed date filter params.
            }
        }

        $reservationFilter = strtolower(trim((string) $request->query('reservation', '')));
        if ($reservationFilter !== '') {
            $today = Carbon::now('America/Los_Angeles')->startOfDay();
            $tomorrow = $today->copy()->addDay();
            $endOfWeek = $today->copy()->endOfWeek();

            if ($reservationFilter === 'upcoming') {
                $query->whereDate('package_use_date', '>', $today->toDateString());
            } elseif ($reservationFilter === 'today') {
                $query->whereDate('package_use_date', $today->toDateString());
            } elseif ($reservationFilter === 'weekend') {
                $query->whereRaw("DATE(package_use_date) >= ? AND DATE(package_use_date) <= ? AND DAYOFWEEK(package_use_date) IN (6, 7)", [
                    $tomorrow->toDateString(),
                    $endOfWeek->toDateString()
                ]);
            } elseif ($reservationFilter === 'past') {
                $query->whereDate('package_use_date', '<', $today->toDateString());
            }
        }

        return $query
            ->with(['event', 'package', 'website', 'affiliate.user', 'entertainer.user'])
            ->latest()
            ->get();
    }

    public function reservation_store($slug, Request $request)
    {
        // ========== BOT PREVENTION - LAYER 1: reCAPTCHA v3 (OPTIONAL) ==========
        $recaptchaToken = $request->input('recaptcha_token');
        if ($recaptchaToken && config('services.recaptcha.secret_key') && config('services.recaptcha.secret_key') !== 'YOUR_RECAPTCHA_SECRET_KEY_HERE') {
            $recaptchaService = new \App\Services\RecaptchaService();
            $recaptchaResult = $recaptchaService->verify($recaptchaToken);

            if (!$recaptchaResult['success']) {
                \Log::info('reCAPTCHA score low', [
                    'score' => $recaptchaResult['score'],
                    'ip' => $request->ip(),
                    'email' => $request->input('reservation_email')
                ]);
                // Don't block here - let Layer 3 validation provide defense
            }
        }

        // ========== BOT PREVENTION - LAYER 3: Server-Side Validation (PRIMARY DEFENSE) ==========
        $validationData = [
            'email' => $request->input('reservation_email'),
            'phone' => $request->input('reservation_phone'),
            'name' => $request->input('reservation_first_name') . ' ' . $request->input('reservation_last_name'),
            'form_load_time' => $request->input('form_load_time'),
            'men_count' => $request->input('men_count'),
            'women_count' => $request->input('women_count'),
            'reservation_date' => $request->input('package_use_date'),
        ];

        $validationResult = \App\Services\FormValidationService::validateReservation($validationData, $request->ip());
        if (!$validationResult['valid']) {
            \Log::warning('Reservation rejected by server validation', [
                'errors' => $validationResult['errors'],
                'ip' => $request->ip(),
                'email' => $request->input('reservation_email')
            ]);
            // Show user-friendly error message
            $errorMessage = count($validationResult['errors']) > 0 ? $validationResult['errors'][0] : 'Submission failed. Please try again.';
            return redirect()->back()->with('error', $errorMessage)->withInput();
        }

        if ($request->filled('event_id')) {
            $requestedGuests = max(0, (int) $request->input('men_count')) + max(0, (int) $request->input('women_count'));
            $this->ensureEventCapacityAvailable(
                Event::find($request->input('event_id')),
                $requestedGuests
            );
        }

        if (isset($request->event_id)) {
            # code...
            $event = Event::findOrFail($request->input('event_id'));
            $website = Website::findOrFail($event->website_id);
        }else{
            $event = null;
            $website = Website::findOrFail($request->website_id);
            
        }

            $ipAddress = $request->ip();

            $confirmationNumber = $this->generateConfirmationNumber();

            $new = new Transaction;
            $new->transaction_id = $confirmationNumber;
            $new->ticket_qr_code = $this->generateTicketQrCode();
            $new->package_first_name = $request->input('reservation_first_name');
            $new->package_last_name = $request->input('reservation_last_name');
            $new->package_phone = $request->input('reservation_phone');
            $new->package_email = $request->input('reservation_email');
            $new->ip_address = $ipAddress;
            // Merge package DOB
            $package_month = $request->input('reservation_month');
            $package_day = $request->input('reservation_day');
            $package_year = $request->input('reservation_year');
            $new->package_dob = ($package_year && $package_month && $package_day) ? (sprintf('%04d-%02d-%02d', $package_year, $package_month, $package_day)) : null;
            $new->package_note = $request->input('reservation_description');
            $new->package_use_date = $request->input('package_use_date');
            $new->event_id = $request->input('event_id');
            $new->website_id = $event != null ? $event->website_id : $request->website_id;
            $new->total = 0; // No payment required for free reservations
            $new->type = 'reservation';
            $new->men = $request->men_count;
            $new->women = $request->women_count;
            $new->save();
            $this->applyReferralCommission($request, $new);

            try {
                        $mailData = [
                            'transaction_id' => $confirmationNumber,
                            'package_first_name' => $new->package_first_name,
                            'package_last_name' => $new->package_last_name,
                            'package_phone' => $new->package_phone,
                            'package_email' => $new->package_email,
                            'package_dob' => $new->package_dob,
                            'package_note' => $new->package_note,
                            'reservation_date' => $new->package_use_date,
                            'package_use_date' => $new->package_use_date,
                            'event_id' => $new->event_id,
                            'website_id' => $new->website_id,
                            'total' => 0,
                            'type' => 'reservation',
                            'ticket_qr_code' => $new->ticket_qr_code,
                            'ticket_qr_image_url' => $this->buildTicketQrImageUrl($new->ticket_qr_code),
                            'men' => (int) $new->men,
                            'women' => (int) $new->women,
                            'guest_count' => max(0, (int) $new->men) + max(0, (int) $new->women),
                            'event_name' => optional($event)->name,
                            'event_date' => optional($event)->date,
                        ];

                        $website = Website::findOrFail($new->website_id);
                        $mailData['club_name'] = $website->name;
                        $mailData['website_name'] = $website->name;
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        $clubEmails = collect($website->emails ?? [])
                            ->pluck('email')
                            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                            ->unique()
                            ->values();

                        $managerMail = new \App\Mail\TransactionMail($mailData, $new, [], null, $website, false, 'manager');
                        foreach ($clubEmails as $clubEmail) {
                            \Illuminate\Support\Facades\Mail::to($clubEmail)->send(clone $managerMail);
                        }

                        $guestEmail = $new->package_email;
                        if ($guestEmail && filter_var($guestEmail, FILTER_VALIDATE_EMAIL)) {
                            $guestMail = new \App\Mail\TransactionMail($mailData, $new, [], null, $website, true, 'guest');
                            \Illuminate\Support\Facades\Mail::to($guestEmail)->send($guestMail);
                        }

                        // ========== SEND SMS NOTIFICATION ==========
                        $guestPhone = $new->package_phone;
                        if ($guestPhone) {
                            try {
                                \Log::info('Attempting to send reservation SMS', ['phone' => $guestPhone]);
                                $smsService = new \App\Services\TelnyxSmsService();
                                $smsData = [
                                    'transaction_id' => $new->transaction_id,
                                    'club_name' => $website->name ?? 'Venue',
                                    'club_slug' => $website->slug ?? '',
                                    'reservation_date' => $new->package_use_date,
                                    'men_count' => $new->package_men ?? 0,
                                    'women_count' => $new->package_women ?? 0,
                                    'total_amount' => $new->total,
                                    'notes' => $new->package_note ?? '',
                                ];
                                $result = $smsService->sendTransactionNotification($guestPhone, $smsData, 'reservation');
                                \Log::info('SMS result for reservation', ['result' => $result]);
                            } catch (\Exception $smsError) {
                                \Log::error('SMS notification failed for reservation: ' . $smsError->getMessage(), ['trace' => $smsError->getTraceAsString()]);
                                // Don't throw error - SMS failure shouldn't block transaction
                            }
                        } else {
                            \Log::warning('No phone number provided for reservation SMS');
                        }
                    } catch (\Throwable $th) {
                        report($th);
                        throw ValidationException::withMessages([
                            'email' => 'Email delivery failed: ' . $th->getMessage(),
                        ]);
                    }

            // Redirect to thank you page with transaction details
            return redirect()->route('thank-you')
                ->with('transaction', $new->fresh())
                ->with('website', $website)
                ->with('paymentType', 'reservation')
                ->with('success', 'Reservation successful!');
        


    }

    public function show($id)
    {
        $user = auth()->user();
        $transaction = Transaction::with(['event', 'package'])->findOrFail($id);
        
        // Check if user has access to this transaction
        if ($user->isWebsiteUser() && $user->website_id) {
            $hasAccess = false;
            
            // Check if transaction belongs to user's website through event
            if ($transaction->event && $transaction->event->website_id == $user->website_id) {
                $hasAccess = true;
            }
            
            // Check if transaction belongs to user's website through package
            if ($transaction->package && $transaction->package->website_id == $user->website_id) {
                $hasAccess = true;
            }
            
            if (!$hasAccess) {
                abort(403, 'Access denied. You do not have permission to view this transaction.');
            }
        }
        
        return view('admin.transaction.show', compact('transaction'));
    }

    public function details($id)
    {
        $user = auth()->user();
        $transaction = Transaction::with(['event', 'package'])->findOrFail($id);

        // Check if user has access to this transaction
        if ($user->isWebsiteUser() && $user->website_id) {
            $hasAccess = false;

            // Check if transaction belongs to user's website through event
            if ($transaction->event && $transaction->event->website_id == $user->website_id) {
                $hasAccess = true;
            }

            // Check if transaction belongs to user's website through package
            if ($transaction->package && $transaction->package->website_id == $user->website_id) {
                $hasAccess = true;
            }

            if (!$hasAccess) {
                abort(403, 'Access denied.');
            }
        }

        // Format data for modal display
        $affiliateName = $transaction->affiliate ? ($transaction->affiliate->display_name ?: optional($transaction->affiliate->user)->name) : '';
        $entertainerName = $transaction->entertainer ? ($transaction->entertainer->display_name ?: optional($transaction->entertainer->user)->name) : '';

        $html = '<div class="row">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Transaction ID:</strong> <span>' . htmlspecialchars($transaction->transaction_id ?? '') . '</span></li>
                            <li class="list-group-item"><strong>IP Address:</strong> <span>' . htmlspecialchars($transaction->ip_address ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Order Items:</strong> <span>' . htmlspecialchars($transaction->package_table_label ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Package Date Of Use:</strong> <span>' . htmlspecialchars($transaction->package_use_date ?? '') . '</span></li>
                            <li class="list-group-item"><strong>First Name:</strong> <span>' . htmlspecialchars($transaction->package_first_name ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Last Name:</strong> <span>' . htmlspecialchars($transaction->package_last_name ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Phone:</strong> <span>' . htmlspecialchars($transaction->package_phone ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Email:</strong> <span>' . htmlspecialchars($transaction->package_email ?? '') . '</span></li>
                            <li class="list-group-item"><strong>DOB:</strong> <span>' . htmlspecialchars($transaction->package_dob ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Note:</strong> <span>' . htmlspecialchars($transaction->package_note ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Number of Guests:</strong> <span>' . htmlspecialchars($transaction->package_number_of_guest ?? '') . '</span></li>';

        // Show guest breakdown for reservation types
        if (strtolower($transaction->type ?? '') === 'reservation') {
            $menCount = (int)($transaction->package_men ?? 0);
            $womenCount = (int)($transaction->package_women ?? 0);
            if ($menCount > 0 || $womenCount > 0) {
                $totalGuests = $menCount + $womenCount;
                $html .= '<li class="list-group-item"><strong>Guest Breakdown:</strong> <span style="font-weight: bold; color: #fbbf24;">' . htmlspecialchars($menCount . ' Men + ' . $womenCount . ' Women = ' . $totalGuests . ' Total') . '</span></li>';
            }
        }

        $html .= '<li class="list-group-item"><strong>Male Guests:</strong> <span>' . htmlspecialchars($transaction->package_men ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Female Guests:</strong> <span>' . htmlspecialchars($transaction->package_women ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Transportation Pickup Time:</strong> <span>' . htmlspecialchars($transaction->transportation_pickup_time ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Transportation Address:</strong> <span>' . htmlspecialchars($transaction->transportation_address ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Transportation Phone:</strong> <span>' . htmlspecialchars($transaction->transportation_phone ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Transportation Guest:</strong> <span>' . htmlspecialchars($transaction->transportation_guest ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Transportation Note:</strong> <span>' . htmlspecialchars($transaction->transportation_note ?? '') . '</span></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Payment First Name:</strong> <span>' . htmlspecialchars($transaction->payment_first_name ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Last Name:</strong> <span>' . htmlspecialchars($transaction->payment_last_name ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Phone:</strong> <span>' . htmlspecialchars($transaction->payment_phone ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Email:</strong> <span>' . htmlspecialchars($transaction->payment_email ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Address:</strong> <span>' . htmlspecialchars($transaction->payment_address ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment City:</strong> <span>' . htmlspecialchars($transaction->payment_city ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment State:</strong> <span>' . htmlspecialchars($transaction->payment_state ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Country:</strong> <span>' . htmlspecialchars($transaction->payment_country ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment DOB:</strong> <span>' . htmlspecialchars($transaction->payment_dob ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Payment Zip Code:</strong> <span>' . htmlspecialchars($transaction->payment_zip_code ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Business Company Name:</strong> <span>' . htmlspecialchars($transaction->business_company ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Business Vat Number:</strong> <span>' . htmlspecialchars($transaction->business_vat ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Business Address:</strong> <span>' . htmlspecialchars($transaction->business_address ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Business Purpose:</strong> <span>' . htmlspecialchars($transaction->business_purpose ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Type:</strong> <span>' . htmlspecialchars($transaction->type ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Status:</strong> <span>';

        $status = $transaction->status;
        if ($status == 1 || $status === 'Completed' || $status === 'Approved') {
            $html .= '<span class="badge bg-success">Completed</span>';
        } elseif ($status == 0 || $status === 'Canceled' || $status === '0') {
            $html .= '<span class="badge bg-danger">Canceled</span>';
        } elseif ($status == 2 || $status === 'Refunded') {
            $html .= '<span class="badge bg-warning text-dark">Refunded</span>';
        } else {
            $html .= '<span class="badge bg-secondary">Unknown</span>';
        }

        $html .= '</span></li>
                            <li class="list-group-item"><strong>Website ID:</strong> <span>' . htmlspecialchars($transaction->website_id ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Event ID:</strong> <span>' . htmlspecialchars($transaction->event_id ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Promo Code:</strong> <span>' . htmlspecialchars($transaction->promo_code ?? '') . '</span></li>
                            <li class="list-group-item"><strong>Discounted Amount:</strong> <span>$' . number_format((float)($transaction->discount ?? 0), 2) . '</span></li>
                            <li class="list-group-item"><strong>Total Amount:</strong> <span>$' . number_format((float)($transaction->sub_total ?? 0), 2) . '</span></li>
                            <li class="list-group-item"><strong>Gratuity:</strong> <span>$' . number_format((float)($transaction->gratuity ?? 0), 2) . '</span></li>
                            <li class="list-group-item"><strong>Non refundable deposit:</strong> <span>$' . number_format((float)($transaction->refundable ?? 0), 2) . '</span></li>
                            <li class="list-group-item"><strong>Total Amount Paid:</strong> <span>$' . number_format((float)($transaction->total ?? 0), 2) . '</span></li>
                            <li class="list-group-item"><strong>Total Due:</strong> <span>$' . number_format((float)($transaction->due ?? 0), 2) . '</span></li>';

        $totalCommission = (float)($transaction->affiliate_commission_amount ?? 0) + (float)($transaction->entertainer_commission_amount ?? 0);
        $html .= '<li class="list-group-item"><strong>Total Commission:</strong> <span>$' . number_format($totalCommission, 2) . '</span></li>';

        $source = 'Direct';
        if ($affiliateName) {
            $source = 'affiliate - ' . $affiliateName;
        } elseif ($entertainerName) {
            $source = 'Entertainer - ' . $entertainerName;
        }
        $html .= '<li class="list-group-item"><strong>Commission Source:</strong> <span>' . htmlspecialchars($source) . '</span></li>';

        if ($affiliateName || ((float)($transaction->affiliate_commission_amount ?? 0) > 0) || ((float)($transaction->affiliate_commission_percentage ?? 0) > 0) || $transaction->affiliate_commission_status) {
            $affText = ($affiliateName ?: 'N/A')
                . ' | ' . number_format((float)($transaction->affiliate_commission_percentage ?? 0), 2) . '%'
                . ' | $' . number_format((float)($transaction->affiliate_commission_amount ?? 0), 2)
                . ($transaction->affiliate_commission_status ? (' | ' . strtoupper($transaction->affiliate_commission_status)) : '');
            $html .= '<li class="list-group-item"><strong>affiliate Commission:</strong> <span>' . htmlspecialchars($affText) . '</span></li>';
        }

        if ($entertainerName || ((float)($transaction->entertainer_commission_amount ?? 0) > 0) || ((float)($transaction->entertainer_commission_percentage ?? 0) > 0) || $transaction->entertainer_commission_status) {
            $entText = ($entertainerName ?: 'N/A')
                . ' | ' . number_format((float)($transaction->entertainer_commission_percentage ?? 0), 2) . '%'
                . ' | $' . number_format((float)($transaction->entertainer_commission_amount ?? 0), 2)
                . ($transaction->entertainer_commission_status ? (' | ' . strtoupper($transaction->entertainer_commission_status)) : '');
            $html .= '<li class="list-group-item"><strong>Entertainer Commission:</strong> <span>' . htmlspecialchars($entText) . '</span></li>';
        }

        $html .= '<li class="list-group-item"><strong>Date (Pacific Time):</strong> <span>' . ($transaction->created_at ? $transaction->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A') : '') . '</span></li>
                            <li class="list-group-item"><strong>Accepted Terms and Conditions:</strong> <span>Yes</span></li>
                            <li class="list-group-item"><strong>Accepted SMS:</strong> <span>Yes</span></li>
                        </ul>
                    </div>
                </div>';

        return response($html, 200, ['Content-Type' => 'text/html']);
    }

    public function update($id, $status)
    {
        $user = auth()->user();
        $change = Transaction::findOrFail($id);
        
        // Check if user has access to this transaction
        if ($user->isWebsiteUser() && $user->website_id) {
            $hasAccess = false;
            
            // Check if transaction belongs to user's website through event
            if ($change->event && $change->event->website_id == $user->website_id) {
                $hasAccess = true;
            }
            
            // Check if transaction belongs to user's website through package
            if ($change->package && $change->package->website && $change->package->website->id == $user->website_id) {
                $hasAccess = true;
            }
            
            if (!$hasAccess) {
                abort(403, 'Access denied. You do not have permission to modify this transaction.');
            }
        }
        
        $previousStatus = (string) $change->status;
        $change->status = $status;
        $change->update();

        // Reverse approved commissions if transaction is canceled or refunded.
        if (in_array((string) $status, ['0', '2'], true) && !in_array($previousStatus, ['0', '2'], true)) {
            $this->reverseCommissionsIfNeeded($change);
        }

        return back();
    }

    /**
     * Show thank you page after successful payment
     */
    public function thankYou()
    {
        // Get data from session (passed from payment processing)
        $transaction = session('transaction');
        $invoice = session('invoice');
        $website = session('website');
        $paymentType = session('paymentType');

        $priceBreakdown = null;
        if ($transaction instanceof Transaction && (string) $transaction->type === 'package') {
            $breakdownWebsite = $website instanceof Website ? $website : Website::find($transaction->website_id);
            $priceBreakdown = $this->buildPackagePriceBreakdown($transaction, $breakdownWebsite);
        }

        return view('thank-you', compact('transaction', 'invoice', 'website', 'paymentType', 'priceBreakdown'));
    }

    public function scanPage()
    {
        $this->ensureScannerAccess();

        return view('admin.transaction.scanner');
    }

    public function viewCheckinPhoto($transactionId)
    {
        // Security: Only allow authenticated admin users to view photos
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to check-in photos.');
        }

        $transaction = Transaction::findOrFail($transactionId);

        if (!$transaction->checkin_photo_path) {
            abort(404, 'No photo available for this check-in.');
        }

        $filePath = storage_path('app/private/' . $transaction->checkin_photo_path);

        if (!file_exists($filePath)) {
            abort(404, 'Photo file not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=3600',
            'Pragma' => 'public',
        ]);
    }

    public function getIdPhotos($transactionId)
    {
        // Security: Only allow authenticated admin/staff users to view photos
        if (!auth()->check() || !in_array(auth()->user()->user_type, ['admin', 'website_user'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $transaction = Transaction::findOrFail($transactionId);

        $frontPhotoUrl = null;
        $backPhotoUrl = null;

        // Check for front ID photo
        if ($transaction->checkin_photo_front_path && file_exists(storage_path('app/private/' . $transaction->checkin_photo_front_path))) {
            $frontPhotoUrl = route('admin.transaction.id-photo', ['transactionId' => $transactionId, 'side' => 'front']);
        }

        // Check for back ID photo
        if ($transaction->checkin_photo_back_path && file_exists(storage_path('app/private/' . $transaction->checkin_photo_back_path))) {
            $backPhotoUrl = route('admin.transaction.id-photo', ['transactionId' => $transactionId, 'side' => 'back']);
        }

        return response()->json([
            'frontPhotoUrl' => $frontPhotoUrl,
            'backPhotoUrl' => $backPhotoUrl,
        ]);
    }

    public function getIdPhoto($transactionId, $side)
    {
        // Security: Only allow authenticated admin/staff users to view photos
        if (!auth()->check() || !in_array(auth()->user()->user_type, ['admin', 'website_user'])) {
            abort(403, 'Unauthorized access to ID photos.');
        }

        $transaction = Transaction::findOrFail($transactionId);

        $photoPath = null;
        if ($side === 'front' && $transaction->checkin_photo_front_path) {
            $photoPath = storage_path('app/private/' . $transaction->checkin_photo_front_path);
        } elseif ($side === 'back' && $transaction->checkin_photo_back_path) {
            $photoPath = storage_path('app/private/' . $transaction->checkin_photo_back_path);
        }

        if (!$photoPath || !file_exists($photoPath)) {
            abort(404, 'Photo not found.');
        }

        return response()->file($photoPath, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=3600',
            'Pragma' => 'public',
        ]);
    }

    public function scanLookup(Request $request)
    {
        $this->ensureScannerAccess();

        $rawCode = (string) $request->query('ticket_qr_code', '');
        $ticketCode = $this->normalizeTicketCode($rawCode);

        if (!$ticketCode) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket code is missing or invalid.',
            ], 422);
        }

        $transaction = $this->scannerTransactionQuery()
            ->where('ticket_qr_code', $ticketCode)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found for this portal.',
            ], 404);
        }

        $transactionType = (string) $transaction->type;
        if (($transactionType !== 'package' && $transactionType !== 'reservation') || (string) $transaction->status !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'This ticket is not valid or not yet approved.',
            ], 422);
        }

        $eventName = trim((string) optional($transaction->event)->name);
        if ($eventName === '') {
            $eventName = trim((string) optional(optional($transaction->package)->event)->name);
        }

        $storedCartItems = $this->normalizeStoredCartItems($transaction->cart_items);
        $packageDetails = collect($storedCartItems)
            ->map(function ($item) {
                if (!is_array($item)) {
                    return null;
                }

                $packageName = trim((string) ($item['package_name'] ?? $item['packageName'] ?? $item['pkgName'] ?? ''));
                if ($packageName === '') {
                    return null;
                }

                $addons = collect((array) ($item['addons'] ?? []))
                    ->map(function ($addon) {
                        if (is_array($addon)) {
                            $name = trim((string) ($addon['name'] ?? ''));
                            return $name !== '' ? $name : null;
                        }

                        $name = trim((string) $addon);
                        return $name !== '' ? $name : null;
                    })
                    ->filter()
                    ->values()
                    ->all();

                return [
                    'package_name' => $packageName,
                    'guests' => max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1)),
                    'addons' => $addons,
                ];
            })
            ->filter()
            ->values()
            ->all();

        if (empty($packageDetails)) {
            $fallbackPackageName = trim((string) optional($transaction->package)->name);
            if ($fallbackPackageName === '') {
                $fallbackPackageName = 'Package';
            }

            $packageDetails = [[
                'package_name' => $fallbackPackageName,
                'guests' => max(1, (int) $transaction->package_number_of_guest),
                'addons' => [],
            ]];
        }

        // Get men and women counts
        $menCount = (int) ($transaction->package_men ?? $transaction->men ?? 0);
        $womenCount = (int) ($transaction->package_women ?? $transaction->women ?? 0);

        // Calculate total guests from men + women, or use stored total
        $totalGuests = $menCount + $womenCount;
        if ($totalGuests <= 0) {
            $totalGuests = (int) collect($packageDetails)->sum('guests');
            if ($totalGuests <= 0) {
                $totalGuests = max(1, (int) $transaction->package_number_of_guest);
            }
        }

        // Format use date in PST with time (default to midnight if no time)
        $useDateFormatted = '-';
        if ($transaction->package_use_date) {
            try {
                $useDate = \Carbon\Carbon::createFromFormat('Y-m-d', $transaction->package_use_date);
                $useDateFormatted = $useDate->setTimezone('America/Los_Angeles')->format('l, M d, Y \a\t 12:00 AM PT');
            } catch (\Exception $e) {
                $useDateFormatted = $transaction->package_use_date;
            }
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'ticket_qr_code' => $transaction->ticket_qr_code,
                'type' => $transactionType === 'reservation' ? 'Reservation' : 'Package',
                'guest_name' => trim(($transaction->package_first_name ?? '') . ' ' . ($transaction->package_last_name ?? '')),
                'package_email' => $transaction->package_email,
                'package_phone' => $transaction->package_phone,
                'website_name' => optional($transaction->website)->name,
                'event_name' => $eventName !== '' ? $eventName : null,
                'total' => number_format((float) $transaction->total, 2, '.', ''),
                'package_use_date' => $useDateFormatted,
                'men_count' => $menCount,
                'women_count' => $womenCount,
                'package_details' => $packageDetails,
                'total_guests' => $totalGuests,
                'checked_in_status' => (bool) $transaction->checked_in_status,
                'checked_in_at_pacific' => optional($transaction->checked_in_at_pacific)->format('Y-m-d h:i A') . (optional($transaction->checked_in_at_pacific)->format('Y-m-d h:i A') ? ' PT' : ''),
            ],
        ]);
    }

    public function scanCheckIn(Request $request)
    {
        $this->ensureScannerAccess();

        $request->validate([
            'ticket_qr_code' => ['required', 'string'],
            'photo_data_front' => ['nullable', 'string'], // Base64 encoded front ID photo
            'photo_data_back' => ['nullable', 'string'], // Base64 encoded back ID photo
        ]);

        $ticketCode = $this->normalizeTicketCode((string) $request->input('ticket_qr_code'));

        if (!$ticketCode) {
            return redirect()->route('admin.transaction.scan')->with('error', 'Ticket code is invalid.');
        }

        $transaction = $this->scannerTransactionQuery()
            ->where('ticket_qr_code', $ticketCode)
            ->first();

        if (!$transaction) {
            return redirect()->route('admin.transaction.scan')->with('error', 'Ticket not found.');
        }

        if ((bool) $transaction->checked_in_status) {
            return redirect()->route('admin.transaction.scan')->with('success', 'Ticket was already checked in.');
        }

        // Handle ID photo uploads (front and back)
        $photoPathFront = null;
        $photoPathBack = null;
        $photoDataFront = $request->input('photo_data_front');
        $photoDataBack = $request->input('photo_data_back');

        try {
            // Create secure directory path (outside web root)
            $storagePath = storage_path('app/private/ticket-checkins');
            if (!is_dir($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            // Handle front ID photo
            if ($photoDataFront && strpos($photoDataFront, 'data:image') === 0) {
                try {
                    $imageData = base64_decode(explode(',', $photoDataFront)[1] ?? '');
                    if ($imageData) {
                        $fileName = 'checkin-' . $transaction->id . '-front-' . time() . '.jpg';
                        $filePath = $storagePath . '/' . $fileName;
                        file_put_contents($filePath, $imageData);
                        $photoPathFront = 'ticket-checkins/' . $fileName;
                    }
                } catch (\Exception $e) {
                    \Log::error('Front ID photo upload failed: ' . $e->getMessage());
                }
            }

            // Handle back ID photo
            if ($photoDataBack && strpos($photoDataBack, 'data:image') === 0) {
                try {
                    $imageData = base64_decode(explode(',', $photoDataBack)[1] ?? '');
                    if ($imageData) {
                        $fileName = 'checkin-' . $transaction->id . '-back-' . time() . '.jpg';
                        $filePath = $storagePath . '/' . $fileName;
                        file_put_contents($filePath, $imageData);
                        $photoPathBack = 'ticket-checkins/' . $fileName;
                    }
                } catch (\Exception $e) {
                    \Log::error('Back ID photo upload failed: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            \Log::error('ID photo upload error: ' . $e->getMessage());
        }

        $transaction->checked_in_status = true;
        $transaction->checked_in_at_pacific = Carbon::now('America/Los_Angeles');
        $transaction->checked_in_by_user_id = auth()->id();

        // Store photo paths if captured
        if ($photoPathFront && Schema::hasColumn('transactions', 'checkin_photo_front_path')) {
            $transaction->checkin_photo_front_path = $photoPathFront;
        }
        if ($photoPathBack && Schema::hasColumn('transactions', 'checkin_photo_back_path')) {
            $transaction->checkin_photo_back_path = $photoPathBack;
        }

        $transaction->save();

        $photoStatus = '';
        if ($photoPathFront || $photoPathBack) {
            $photoStatus = ' (Both ID photos captured).';
        }

        return redirect()->route('admin.transaction.scan')
            ->with('success', 'Check-in completed for ticket #' . $transaction->ticket_qr_code . $photoStatus);
    }

    private function extractCartItemsFromRequest(Request $request): array
    {
        $rawCartItems = $request->input('cart_items');
        $decodedItems = [];

        if (is_string($rawCartItems) && trim($rawCartItems) !== '') {
            $decoded = json_decode($rawCartItems, true);
            if (is_array($decoded)) {
                $decodedItems = $decoded;
            }
        } elseif (is_array($rawCartItems)) {
            $decodedItems = $rawCartItems;
        }

        if (empty($decodedItems)) {
            $packageId = (int) $request->input('package_id');
            if ($packageId <= 0) {
                return [];
            }

            $package = Package::find($packageId);
            $addons = collect(explode(',', (string) $request->input('addons')))
                ->map(fn ($name) => trim($name))
                ->filter()
                ->map(fn ($name) => ['name' => $name])
                ->values()
                ->all();

            return [[
                'package_id' => $packageId,
                'package_name' => optional($package)->name,
                'guests' => max(1, (int) $request->input('package_number_of_guest', 1)),
                'is_multiple' => optional($package)->multiple,
                'unit_price' => (float) optional($package)->price,
                'line_total' => (float) optional($package)->price,
                'addons' => $addons,
                'transportation' => optional($package)->transportation,
            ]];
        }

        return collect($decodedItems)->map(function ($item) {
            $packageId = (int) ($item['package_id'] ?? $item['packageId'] ?? $item['pkgId'] ?? 0);
            $package = $packageId > 0 ? Package::find($packageId) : null;
            $guests = max(1, (int) ($item['guests'] ?? 1));
            $isMultiple = $this->isTruthy($item['is_multiple'] ?? $item['isMultiple'] ?? optional($package)->multiple);
            $unitPrice = (float) ($item['unit_price'] ?? $item['packagePrice'] ?? $item['pkgPrice'] ?? optional($package)->price ?? 0);
            $lineTotal = (float) ($item['line_total'] ?? ($unitPrice * ($isMultiple ? $guests : 1)));
            $addons = collect($item['addons'] ?? [])->map(function ($addon) {
                if (!is_array($addon)) {
                    return null;
                }

                $addonId = (int) ($addon['id'] ?? 0);
                $qty = (int) ($addon['qty'] ?? $addon['quantity'] ?? 0);
                $linePrice = isset($addon['price']) ? (float) $addon['price'] : 0;
                $unitPrice = isset($addon['unit_price'])
                    ? (float) $addon['unit_price']
                    : 0;

                if ($unitPrice <= 0 && $qty > 0 && $linePrice > 0) {
                    $unitPrice = $linePrice / $qty;
                }

                if (($unitPrice <= 0 || $qty <= 0) && $addonId > 0) {
                    $catalogUnit = (float) optional(Addon::find($addonId))->price;
                    if ($catalogUnit > 0) {
                        if ($unitPrice <= 0) {
                            $unitPrice = $catalogUnit;
                        }
                        if ($qty <= 0 && $linePrice > 0) {
                            $estimatedQty = (int) round($linePrice / $catalogUnit);
                            if ($estimatedQty > 0) {
                                $qty = $estimatedQty;
                            }
                        }
                    }
                }

                if ($qty <= 0) {
                    $qty = 1;
                }

                if ($unitPrice <= 0) {
                    $unitPrice = $qty > 0 ? ($linePrice / $qty) : $linePrice;
                }

                return [
                    'id' => $addonId > 0 ? $addonId : null,
                    'name' => $addon['name'] ?? '',
                    'qty' => $qty,
                    'unit_price' => $unitPrice,
                    'price' => $linePrice,
                ];
            })->filter()->values()->all();

            return [
                'package_id' => $packageId,
                'package_name' => $item['package_name'] ?? $item['packageName'] ?? $item['pkgName'] ?? optional($package)->name,
                'guests' => $guests,
                'is_multiple' => $isMultiple,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'addons' => $addons,
                'transportation' => $item['transportation'] ?? $item['transport'] ?? optional($package)->transportation,
            ];
        })->values()->all();
    }

    private function summarizeCartItems(array $cartItems): array
    {
        $primaryPackageId = (int) ($cartItems[0]['package_id'] ?? 0);
        $totalGuests = collect($cartItems)->sum(function (array $item) {
            return max(1, (int) ($item['guests'] ?? 1));
        });
        $addonsSummary = collect($cartItems)
            ->flatMap(fn (array $item) => $item['addons'] ?? [])
            ->map(fn (array $addon) => trim((string) ($addon['name'] ?? '')))
            ->filter()
            ->implode(', ');

        $commissionBaseAmount = collect($cartItems)->sum(function (array $item) {
            $packageId = (int) ($item['package_id'] ?? 0);
            $package = $packageId > 0 ? Package::find($packageId) : null;

            $guests = max(1, (int) ($item['guests'] ?? 1));
            $isMultiple = $this->isTruthy($item['is_multiple'] ?? optional($package)->multiple);
            $billableGuests = $isMultiple ? $guests : 1;

            $unitPrice = $package ? (float) ($package->price ?? 0) : (float) ($item['unit_price'] ?? 0);
            $lineAmount = $unitPrice * $billableGuests;

            $addonsAmount = collect($item['addons'] ?? [])->sum(function (array $addon) {
                return (float) ($addon['price'] ?? 0);
            });

            return max(0, $lineAmount + $addonsAmount);
        });

        return [
            'primary_package_id' => $primaryPackageId,
            'total_guests' => max(1, (int) $totalGuests),
            'addons_summary' => $addonsSummary,
            'commission_base_amount' => round((float) $commissionBaseAmount, 2),
        ];
    }

    private function cartRequiresTransportation(array $cartItems, ?Package $selectedPackage): bool
    {
        if (!empty($cartItems)) {
            foreach ($cartItems as $item) {
                if ($this->isTruthy($item['transportation'] ?? false)) {
                    return true;
                }
            }
        }

        return $selectedPackage
            && ($selectedPackage->transportation == 1 || $selectedPackage->transportation === true || $selectedPackage->transportation === '1');
    }

    private function isTruthy($value): bool
    {
        return $value === true || $value === 1 || $value === '1' || $value === 'true';
    }

    private function ensureEventCapacityAvailable(?Event $event, int $requestedGuests, ?Carbon $targetDate = null): void
    {
        if (!$event || $event->attendee_limit === null) {
            return;
        }

        $limit = (int) $event->attendee_limit;
        if ($limit <= 0) {
            return;
        }

        $requestedGuests = max(1, $requestedGuests);
        $confirmedAttendees = $this->countConfirmedEventAttendees($event, $targetDate);
        $remainingCapacity = max($limit - $confirmedAttendees, 0);

        if ($requestedGuests > $remainingCapacity) {
            $message = $remainingCapacity > 0
                ? 'Only ' . $remainingCapacity . ' spots remain for this event.'
                : 'This event is sold out.';

            throw ValidationException::withMessages([
                'package_id' => $message,
            ]);
        }
    }

    private function ensureCartEventCapacitiesAvailable(array $cartItems, ?Carbon $targetDate = null): void
    {
        if (empty($cartItems)) {
            return;
        }

        $requestedGuestsByEvent = [];

        foreach ($cartItems as $item) {
            if (!is_array($item)) {
                continue;
            }

            $packageId = (int) ($item['package_id'] ?? 0);
            if ($packageId <= 0) {
                continue;
            }

            $package = Package::find($packageId);
            $eventId = (int) optional($package)->event_id;
            if ($eventId <= 0) {
                continue;
            }

            $guests = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
            $requestedGuestsByEvent[$eventId] = ($requestedGuestsByEvent[$eventId] ?? 0) + $guests;
        }

        foreach ($requestedGuestsByEvent as $eventId => $requestedGuests) {
            $this->ensureEventCapacityAvailable(Event::find($eventId), (int) $requestedGuests, $targetDate);
        }
    }

    private function countConfirmedEventAttendees(Event $event, ?Carbon $targetDate = null): int
    {
        $query = Transaction::query()
            ->where('event_id', $event->id)
            ->where('status', 1);

        if ($targetDate) {
            $dateString = $targetDate->toDateString();
            $query->where(function ($dateQuery) use ($dateString) {
                $dateQuery->whereDate('package_use_date', $dateString)
                    ->orWhere(function ($fallbackDateQuery) use ($dateString) {
                        $fallbackDateQuery->whereNull('package_use_date')
                            ->whereDate('created_at', $dateString);
                    });
            });
        }

        return $query->get(['type', 'package_number_of_guest', 'men', 'women'])
            ->sum(function (Transaction $transaction) {
                if ($transaction->type === 'reservation') {
                    return max(0, (int) $transaction->men) + max(0, (int) $transaction->women);
                }

                return max(1, (int) $transaction->package_number_of_guest);
            });
    }

    private function applyWebsiteSmtpConfig(Website $website): void
    {
        $smtp = optional($website)->smtp;

        if (!$this->hasUsableSmtp($smtp)) {
            $this->applyDefaultSmtpConfig();
            return;
        }

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

    private function applyDefaultSmtpConfig(): void
    {
        $defaultHost = (string) config('mail.mailers.smtp.host');
        $defaultPort = (string) config('mail.mailers.smtp.port');

        if ($defaultHost === '' || $defaultPort === '') {
            throw ValidationException::withMessages([
                'email' => 'Email delivery failed: default SMTP is not configured. Please set MAIL_HOST and MAIL_PORT in .env.',
            ]);
        }

        config([
            'mail.default' => 'smtp',
            'mail.from.address' => config('mail.from.address'),
            'mail.from.name' => config('mail.from.name'),
        ]);
    }

    private function hasUsableSmtp($smtp): bool
    {
        return $smtp
            && !empty($smtp->host)
            && !empty($smtp->port)
            && !empty($smtp->username)
            && !empty($smtp->password);
    }

    private function normalizeSmtpEncryption($value): ?string
    {
        if (in_array($value, ['tls', 'ssl'], true)) {
            return $value;
        }

        if ((string) $value === '1') {
            return 'tls';
        }

        return null;
    }

    private function validateTransportationAvailability(Website $website, Request $request, ?Package $selectedPackage = null): void
    {
        $pickupDate = (string) $request->input('package_use_date');
        $pickupTime = (string) $request->input('transportation_pickup_time');

        if ($pickupDate !== '' && !($selectedPackage && $selectedPackage->event_id) && !$this->isWebsiteOpenOnDate($website, $pickupDate)) {
            throw ValidationException::withMessages([
                'package_use_date' => 'Selected club is closed on that date.',
            ]);
        }

        if ($pickupTime !== '' && !$this->isWithinWebsiteOperatingHours($website, $pickupTime)) {
            throw ValidationException::withMessages([
                'transportation_pickup_time' => 'Pickup time must be within the club operating hours.',
            ]);
        }
    }

    private function isWebsiteOpenOnDate(Website $website, string $pickupDate): bool
    {
        $operatingDays = $this->normalizedOperatingDays($website);

        if ($operatingDays === []) {
            return true;
        }

        try {
            $dayName = strtolower(Carbon::parse($pickupDate)->format('l'));
        } catch (\Throwable $exception) {
            return false;
        }

        return in_array($dayName, $operatingDays, true);
    }

    private function isWithinWebsiteOperatingHours(Website $website, string $pickupTime): bool
    {
        $startMinutes = $this->convertTimeStringToMinutes($website->operating_start_time);
        $endMinutes = $this->convertTimeStringToMinutes($website->operating_end_time);
        $pickupMinutes = $this->convertTimeStringToMinutes($pickupTime);

        if ($pickupMinutes === null) {
            return false;
        }

        if ($startMinutes === null || $endMinutes === null) {
            return true;
        }

        if ($endMinutes < $startMinutes) {
            return $pickupMinutes >= $startMinutes || $pickupMinutes <= $endMinutes;
        }

        return $pickupMinutes >= $startMinutes && $pickupMinutes <= $endMinutes;
    }

    private function normalizedOperatingDays(Website $website): array
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return collect((array) $website->operating_days)
            ->map(fn ($day) => strtolower(trim((string) $day)))
            ->filter(fn ($day) => in_array($day, $validDays, true))
            ->unique()
            ->values()
            ->all();
    }

    private function convertTimeStringToMinutes(?string $time): ?int
    {
        if (!$time) {
            return null;
        }

        foreach (['H:i', 'H:i:s', 'h:i A', 'g:i A'] as $format) {
            try {
                $parsedTime = Carbon::createFromFormat($format, trim($time));

                return ((int) $parsedTime->format('H') * 60) + (int) $parsedTime->format('i');
            } catch (\Throwable $exception) {
            }
        }

        return null;
    }

    private function applyReferralCommission(Request $request, Transaction $transaction, ?float $commissionBaseAmount = null): void
    {
        $affiliateResolved = $this->applyAffiliateCommission($request, $transaction, $commissionBaseAmount);
        if (!$affiliateResolved) {
            $this->applyEntertainerCommission($request, $transaction, $commissionBaseAmount);
        }
    }

    private function applyAffiliateCommission(Request $request, Transaction $transaction, ?float $commissionBaseAmount = null): bool
    {
        $affiliate = $this->resolveAffiliateFromRequest($request);
        if (!$affiliate) {
            return false;
        }

        $packageId = (int) $request->input('package_id');
        if ($packageId <= 0) {
            $transaction->affiliate_id = $affiliate->id;
            $transaction->affiliate_source = $affiliate->slug;
            $transaction->save();
            session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
            return true;
        }

        $mapping = AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->where('package_id', $packageId)
            ->where('is_active', true)
            ->first();

        if (!$mapping) {
            return true;
        }

        $commissionPercentage = (float) ($mapping->commission_percentage ?? $affiliate->default_commission_percentage);
        $baseAmount = $commissionBaseAmount !== null
            ? (float) $commissionBaseAmount
            : (float) ($transaction->actual_total ?? $transaction->total ?? 0);
        $commissionAmount = round(max($baseAmount, 0) * ($commissionPercentage / 100), 2);

        $transaction->affiliate_id = $affiliate->id;
        $transaction->affiliate_commission_percentage = $commissionPercentage;
        $transaction->affiliate_commission_amount = $commissionAmount;
        $transaction->affiliate_commission_status = Transaction::COMMISSION_STATUS_PENDING;
        $transaction->affiliate_commission_hold_until = $this->commissionHoldUntil($transaction);
        $transaction->affiliate_commission_approved_at = null;
        $transaction->affiliate_commission_reversed_at = null;
        $transaction->affiliate_source = $affiliate->slug;
        $transaction->save();

        if ($commissionAmount > 0) {
            AffiliateWalletTransaction::create([
                'affiliate_id' => $affiliate->id,
                'transaction_id' => $transaction->id,
                'type' => 'commission',
                'status' => 'pending',
                'amount' => $commissionAmount,
                'balance_after' => (float) $affiliate->wallet_balance,
                'description' => 'Commission pending hold period for purchase #' . $transaction->id,
                'meta' => [
                    'package_id' => $packageId,
                    'website_id' => $transaction->website_id,
                    'commission_percentage' => $commissionPercentage,
                    'commission_base_amount' => round(max($baseAmount, 0), 2),
                    'commission_amount' => $commissionAmount,
                    'hold_until' => optional($transaction->affiliate_commission_hold_until)->toDateTimeString(),
                ],
            ]);
        }

        session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
        return true;
    }

    private function applyEntertainerCommission(Request $request, Transaction $transaction, ?float $commissionBaseAmount = null): void
    {
        $entertainer = $this->resolveEntertainerFromRequest($request);
        if (!$entertainer) {
            return;
        }

        $packageId = (int) $request->input('package_id');
        if ($packageId <= 0) {
            $transaction->entertainer_id = $entertainer->id;
            $transaction->entertainer_source = $entertainer->slug;
            $transaction->save();
            return;
        }

        $websiteId = (int) $request->input('website_id');

        $mapping = EntertainerPackage::where('entertainer_id', $entertainer->id)
            ->where('package_id', $packageId)
            ->where('website_id', $websiteId)
            ->where('is_active', true)
            ->first();

        if (!$mapping) {
            return;
        }

        $commissionPercentage = (float) ($entertainer->default_commission_percentage ?? 0);
        $baseAmount = $commissionBaseAmount !== null
            ? (float) $commissionBaseAmount
            : (float) ($transaction->actual_total ?? $transaction->total ?? 0);
        $commissionAmount = round(max($baseAmount, 0) * ($commissionPercentage / 100), 2);

        $transaction->entertainer_id = $entertainer->id;
        $transaction->entertainer_commission_percentage = $commissionPercentage;
        $transaction->entertainer_commission_amount = $commissionAmount;
        $transaction->entertainer_commission_status = Transaction::COMMISSION_STATUS_PENDING;
        $transaction->entertainer_commission_hold_until = $this->commissionHoldUntil($transaction);
        $transaction->entertainer_commission_approved_at = null;
        $transaction->entertainer_commission_reversed_at = null;
        $transaction->entertainer_source = $entertainer->slug;
        $transaction->save();

        if ($commissionAmount > 0) {
            EntertainerWalletTransaction::create([
                'entertainer_id' => $entertainer->id,
                'transaction_id' => $transaction->id,
                'type' => 'commission',
                'status' => 'pending',
                'amount' => $commissionAmount,
                'balance_after' => (float) $entertainer->wallet_balance,
                'description' => 'Commission pending hold period for purchase #' . $transaction->id,
                'meta' => [
                    'package_id' => $packageId,
                    'website_id' => $transaction->website_id,
                    'commission_percentage' => $commissionPercentage,
                    'commission_base_amount' => round(max($baseAmount, 0), 2),
                    'commission_amount' => $commissionAmount,
                    'hold_until' => optional($transaction->entertainer_commission_hold_until)->toDateTimeString(),
                ],
            ]);
        }
    }

    public function approveMaturedCommissions(): array
    {
        $now = now();

        $transactions = Transaction::query()
            ->where(function ($query) use ($now) {
                $query->where(function ($affiliateQuery) use ($now) {
                    $affiliateQuery->where('affiliate_commission_status', Transaction::COMMISSION_STATUS_PENDING)
                        ->whereNotNull('affiliate_id')
                        ->where('affiliate_commission_amount', '>', 0)
                        ->where(function ($holdQuery) use ($now) {
                            $holdQuery->whereNull('affiliate_commission_hold_until')
                                ->orWhere('affiliate_commission_hold_until', '<=', $now);
                        });
                })->orWhere(function ($entertainerQuery) use ($now) {
                    $entertainerQuery->where('entertainer_commission_status', Transaction::COMMISSION_STATUS_PENDING)
                        ->whereNotNull('entertainer_id')
                        ->where('entertainer_commission_amount', '>', 0)
                        ->where(function ($holdQuery) use ($now) {
                            $holdQuery->whereNull('entertainer_commission_hold_until')
                                ->orWhere('entertainer_commission_hold_until', '<=', $now);
                        });
                });
            })
            ->get();

        $approvedAffiliate = 0;
        $approvedEntertainer = 0;

        foreach ($transactions as $transaction) {
            if ($this->approveAffiliateCommissionIfMatured($transaction, $now)) {
                $approvedAffiliate++;
            }

            if ($this->approveEntertainerCommissionIfMatured($transaction, $now)) {
                $approvedEntertainer++;
            }
        }

        return [
            'transactions_scanned' => $transactions->count(),
            'affiliate_approved' => $approvedAffiliate,
            'entertainer_approved' => $approvedEntertainer,
        ];
    }

    private function approveAffiliateCommissionIfMatured(Transaction $transaction, Carbon $now): bool
    {
        if ((string) $transaction->affiliate_commission_status !== Transaction::COMMISSION_STATUS_PENDING) {
            return false;
        }

        if (!$transaction->affiliate_id || (float) $transaction->affiliate_commission_amount <= 0) {
            return false;
        }

        if ($transaction->affiliate_commission_hold_until && $transaction->affiliate_commission_hold_until->gt($now)) {
            return false;
        }

        $affiliate = Affiliate::find($transaction->affiliate_id);
        if (!$affiliate) {
            return false;
        }

        $commissionAmount = round((float) $transaction->affiliate_commission_amount, 2);
        $newBalance = round((float) $affiliate->wallet_balance + $commissionAmount, 2);

        $affiliate->wallet_balance = $newBalance;
        $affiliate->save();

        AffiliateWalletTransaction::create([
            'affiliate_id' => $affiliate->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'credited',
            'amount' => $commissionAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission approved after hold period for purchase #' . $transaction->id,
            'meta' => [
                'website_id' => $transaction->website_id,
                'commission_percentage' => (float) ($transaction->affiliate_commission_percentage ?? 0),
                'commission_base_amount' => round(max((float) ($transaction->actual_total ?? $transaction->total ?? 0), 0), 2),
                'hold_until' => optional($transaction->affiliate_commission_hold_until)->toDateTimeString(),
                'approved_at' => $now->toDateTimeString(),
            ],
        ]);

        $transaction->affiliate_commission_status = Transaction::COMMISSION_STATUS_APPROVED;
        $transaction->affiliate_commission_approved_at = $now;
        $transaction->save();

        return true;
    }

    private function approveEntertainerCommissionIfMatured(Transaction $transaction, Carbon $now): bool
    {
        if ((string) $transaction->entertainer_commission_status !== Transaction::COMMISSION_STATUS_PENDING) {
            return false;
        }

        if (!$transaction->entertainer_id || (float) $transaction->entertainer_commission_amount <= 0) {
            return false;
        }

        if ($transaction->entertainer_commission_hold_until && $transaction->entertainer_commission_hold_until->gt($now)) {
            return false;
        }

        $entertainer = Entertainer::find($transaction->entertainer_id);
        if (!$entertainer) {
            return false;
        }

        $commissionAmount = round((float) $transaction->entertainer_commission_amount, 2);
        $newBalance = round((float) $entertainer->wallet_balance + $commissionAmount, 2);

        $entertainer->wallet_balance = $newBalance;
        $entertainer->save();

        EntertainerWalletTransaction::create([
            'entertainer_id' => $entertainer->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'credited',
            'amount' => $commissionAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission approved after hold period for purchase #' . $transaction->id,
            'meta' => [
                'website_id' => $transaction->website_id,
                'commission_percentage' => (float) ($transaction->entertainer_commission_percentage ?? 0),
                'commission_base_amount' => round(max((float) ($transaction->actual_total ?? $transaction->total ?? 0), 0), 2),
                'hold_until' => optional($transaction->entertainer_commission_hold_until)->toDateTimeString(),
                'approved_at' => $now->toDateTimeString(),
            ],
        ]);

        $transaction->entertainer_commission_status = Transaction::COMMISSION_STATUS_APPROVED;
        $transaction->entertainer_commission_approved_at = $now;
        $transaction->save();

        return true;
    }

    private function reverseCommissionsIfNeeded(Transaction $transaction): void
    {
        $this->reverseAffiliateCommissionIfNeeded($transaction);
        $this->reverseEntertainerCommissionIfNeeded($transaction);
    }

    private function reverseAffiliateCommissionIfNeeded(Transaction $transaction): void
    {
        if ((string) $transaction->affiliate_commission_status !== Transaction::COMMISSION_STATUS_APPROVED) {
            return;
        }

        if (!$transaction->affiliate_id || (float) $transaction->affiliate_commission_amount <= 0) {
            return;
        }

        $affiliate = Affiliate::find($transaction->affiliate_id);
        if (!$affiliate) {
            return;
        }

        $reverseAmount = round((float) $transaction->affiliate_commission_amount, 2);
        $newBalance = round((float) $affiliate->wallet_balance - $reverseAmount, 2);

        $affiliate->wallet_balance = $newBalance;
        $affiliate->save();

        AffiliateWalletTransaction::create([
            'affiliate_id' => $affiliate->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'reversed',
            'amount' => -$reverseAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission reversed due to canceled/refunded transaction #' . $transaction->id,
            'meta' => [
                'website_id' => $transaction->website_id,
                'reversed_at' => now()->toDateTimeString(),
            ],
        ]);

        $transaction->affiliate_commission_status = Transaction::COMMISSION_STATUS_REVERSED;
        $transaction->affiliate_commission_reversed_at = now();
        $transaction->save();
    }

    private function reverseEntertainerCommissionIfNeeded(Transaction $transaction): void
    {
        if ((string) $transaction->entertainer_commission_status !== Transaction::COMMISSION_STATUS_APPROVED) {
            return;
        }

        if (!$transaction->entertainer_id || (float) $transaction->entertainer_commission_amount <= 0) {
            return;
        }

        $entertainer = Entertainer::find($transaction->entertainer_id);
        if (!$entertainer) {
            return;
        }

        $reverseAmount = round((float) $transaction->entertainer_commission_amount, 2);
        $newBalance = round((float) $entertainer->wallet_balance - $reverseAmount, 2);

        $entertainer->wallet_balance = $newBalance;
        $entertainer->save();

        EntertainerWalletTransaction::create([
            'entertainer_id' => $entertainer->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'reversed',
            'amount' => -$reverseAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission reversed due to canceled/refunded transaction #' . $transaction->id,
            'meta' => [
                'website_id' => $transaction->website_id,
                'reversed_at' => now()->toDateTimeString(),
            ],
        ]);

        $transaction->entertainer_commission_status = Transaction::COMMISSION_STATUS_REVERSED;
        $transaction->entertainer_commission_reversed_at = now();
        $transaction->save();
    }

    private function commissionHoldUntil(Transaction $transaction): Carbon
    {
        $website = $transaction->website ?: Website::find($transaction->website_id);
        $isAuthorize = $website && (string) $website->payment_method === 'authorize';

        if ($isAuthorize) {
            $holdDays = (int) ($website->commission_hold_days_authorize
                ?? env('COMMISSION_HOLD_DAYS_AUTHORIZE', 90));
        } else {
            $holdDays = (int) ($website->commission_hold_days
                ?? env('COMMISSION_HOLD_DAYS', 60));
        }

        return now()->addDays(max($holdDays, 0));
    }

    private function ensureScannerAccess(): void
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isWebsiteUser() && !$user->isBouncer())) {
            abort(403, 'Access denied.');
        }
    }

    private function scannerTransactionQuery()
    {
        $user = auth()->user();

        $query = Transaction::query()->with(['website', 'event', 'package']);

        if ($user && ($user->isWebsiteUser() || $user->isBouncer()) && $user->website_id) {
            $query->where(function ($scopedQuery) use ($user) {
                $scopedQuery->where('website_id', $user->website_id)
                    ->orWhereHas('event', function ($eventQuery) use ($user) {
                        $eventQuery->where('website_id', $user->website_id);
                    })
                    ->orWhereHas('package', function ($packageQuery) use ($user) {
                        $packageQuery->where('website_id', $user->website_id);
                    });
            });
        }

        return $query;
    }

    private function normalizeTicketCode(?string $rawCode): ?string
    {
        $rawCode = trim((string) $rawCode);
        if ($rawCode === '') {
            return null;
        }

        if (filter_var($rawCode, FILTER_VALIDATE_URL)) {
            $query = parse_url($rawCode, PHP_URL_QUERY);
            if (is_string($query)) {
                parse_str($query, $params);
                if (!empty($params['ticket'])) {
                    $rawCode = (string) $params['ticket'];
                }
            }
        }

        if (preg_match('/(CVT-[A-Z0-9]+)/i', $rawCode, $matches)) {
            return strtoupper($matches[1]);
        }

        return strtoupper($rawCode);
    }

    private function buildPackagePriceBreakdown(Transaction $transaction, ?Website $website): array
    {
        $cartItems = $this->normalizeStoredCartItems($transaction->cart_items);

        $items = [];
        $packagesSubtotal = 0.0;
        $addonsSubtotal = 0.0;

        foreach ($cartItems as $item) {
            $guests = max(1, (int) ($item['guests'] ?? 1));
            $isMultiple = $this->isTruthy($item['is_multiple'] ?? false);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $packageSubtotal = $isMultiple ? ($unitPrice * $guests) : $unitPrice;

            $addons = collect((array) ($item['addons'] ?? []))
                ->map(function ($addon) {
                    if (!is_array($addon)) {
                        return null;
                    }

                    $addonId = (int) ($addon['id'] ?? 0);
                    $qty = (int) ($addon['qty'] ?? $addon['quantity'] ?? 0);
                    $linePrice = (float) ($addon['price'] ?? 0);
                    $unitPrice = isset($addon['unit_price'])
                        ? (float) $addon['unit_price']
                        : 0;

                    if ($unitPrice <= 0 && $qty > 0 && $linePrice > 0) {
                        $unitPrice = $linePrice / $qty;
                    }

                    if (($unitPrice <= 0 || $qty <= 0) && $addonId > 0) {
                        $catalogUnit = (float) optional(Addon::find($addonId))->price;
                        if ($catalogUnit > 0) {
                            if ($unitPrice <= 0) {
                                $unitPrice = $catalogUnit;
                            }
                            if ($qty <= 0 && $linePrice > 0) {
                                $estimatedQty = (int) round($linePrice / $catalogUnit);
                                if ($estimatedQty > 0) {
                                    $qty = $estimatedQty;
                                }
                            }
                        }
                    }

                    if ($qty <= 0) {
                        $qty = 1;
                    }

                    if ($unitPrice <= 0) {
                        $unitPrice = $qty > 0 ? ($linePrice / $qty) : $linePrice;
                    }

                    return [
                        'id' => $addonId > 0 ? $addonId : null,
                        'name' => trim((string) ($addon['name'] ?? '')),
                        'qty' => $qty,
                        'unit_price' => round($unitPrice, 2),
                        'price' => round($linePrice, 2),
                    ];
                })
                ->filter(fn ($addon) => $addon !== null && $addon['name'] !== '')
                ->values()
                ->all();

            $addonsTotal = collect($addons)->sum(fn ($addon) => (float) ($addon['price'] ?? 0));
            $lineTotal = $packageSubtotal + $addonsTotal;

            $packagesSubtotal += $packageSubtotal;
            $addonsSubtotal += $addonsTotal;

            $items[] = [
                'package_name' => (string) ($item['package_name'] ?? ('Package #' . ($item['package_id'] ?? ''))),
                'guests' => $guests,
                'is_multiple' => $isMultiple,
                'unit_price' => round($unitPrice, 2),
                'package_subtotal' => round($packageSubtotal, 2),
                'addons' => $addons,
                'addons_total' => round($addonsTotal, 2),
                'line_total' => round($lineTotal, 2),
            ];
        }

        $itemsSubtotal = $packagesSubtotal + $addonsSubtotal;

        // Coupon discount applies only to package subtotal (packages + addons), never on additional charges.
        $promoDiscount = max(0, (float) ($transaction->discounted_amount ?? 0));
        if ($promoDiscount > $itemsSubtotal) {
            $promoDiscount = $itemsSubtotal;
        }
        $discountedItemsSubtotal = max($itemsSubtotal - $promoDiscount, 0);

        $serviceChargeRate = (float) ($website->service_charge_fee ?? 0);
        $serviceChargeName = (string) ($website->service_charge_name ?? 'Service Charge');
        $serviceChargeEnabled = $serviceChargeRate > 0 && trim($serviceChargeName) !== '' && trim($serviceChargeName) !== '0';
        $serviceChargeAmount = $serviceChargeEnabled ? ($discountedItemsSubtotal * $serviceChargeRate / 100) : 0;

        $gratuityRate = (float) ($website->gratuity_fee ?? 0);
        $gratuityName = (string) ($website->gratuity_name ?? 'Gratuity Fee');
        $gratuityEnabled = $gratuityRate > 0 && trim($gratuityName) !== '' && trim($gratuityName) !== '0';
        $gratuityAmount = $gratuityEnabled ? ($discountedItemsSubtotal * $gratuityRate / 100) : 0;

        $salesTaxRate = (float) ($website->sales_tax_fee ?? 0);
        $salesTaxName = (string) ($website->sales_tax_name ?? 'Sales Tax');
        $salesTaxEnabled = $salesTaxRate > 0 && trim($salesTaxName) !== '' && trim($salesTaxName) !== '0';
        $salesTaxBase = $discountedItemsSubtotal;
        $salesTaxAmount = $salesTaxEnabled ? ($salesTaxBase * $salesTaxRate / 100) : 0;

        $afterDiscountTotal = $discountedItemsSubtotal + $serviceChargeAmount + $gratuityAmount + $salesTaxAmount;
        $processingFeeBase = $discountedItemsSubtotal;

        $processingFeeRate = (float) ($website->processing_fee ?? 0);
        $processingFeeType = strtolower((string) ($website->processing_fee_type ?? 'percentage'));
        if ($processingFeeType !== 'flat') {
            $processingFeeType = 'percentage';
        }

        $processingFeeAmount = 0;
        if ($processingFeeRate > 0) {
            $processingFeeAmount = $processingFeeType === 'flat'
                ? $processingFeeRate
                : ($processingFeeBase * $processingFeeRate / 100);
        }

        $calculatedGrandTotal = $afterDiscountTotal + $processingFeeAmount;
        $storedGrandTotal = (float) ($transaction->actual_total ?? 0);
        $grandTotal = $storedGrandTotal > 0 ? $storedGrandTotal : $calculatedGrandTotal;

        $refundableRate = (float) ($website->refundable_fee ?? 0);
        $refundableName = (string) ($website->refundable_name ?? 'Non Refundable Processing Fees');
        $refundableAmount = $refundableRate > 0 ? ($grandTotal * $refundableRate / 100) : 0;

        $amountPaidNow = (float) ($transaction->total ?? 0);
        $remainingDue = max($grandTotal - $amountPaidNow, 0);

        return [
            'items' => $items,
            'packages_subtotal' => round($packagesSubtotal, 2),
            'addons_subtotal' => round($addonsSubtotal, 2),
            'items_subtotal' => round($itemsSubtotal, 2),
            'service_charge' => [
                'enabled' => $serviceChargeEnabled,
                'name' => $serviceChargeName,
                'rate' => round($serviceChargeRate, 4),
                'amount' => round($serviceChargeAmount, 2),
            ],
            'gratuity' => [
                'enabled' => $gratuityEnabled,
                'name' => $gratuityName,
                'rate' => round($gratuityRate, 4),
                'amount' => round($gratuityAmount, 2),
            ],
            'sales_tax' => [
                'enabled' => $salesTaxEnabled,
                'name' => $salesTaxName,
                'rate' => round($salesTaxRate, 4),
                'amount' => round($salesTaxAmount, 2),
            ],
            'promo_discount' => round($promoDiscount, 2),
            'processing_fee' => [
                'enabled' => $processingFeeAmount > 0,
                'type' => $processingFeeType,
                'rate' => round($processingFeeRate, 4),
                'amount' => round($processingFeeAmount, 2),
            ],
            'pre_discount_total' => round($itemsSubtotal, 2),
            'after_discount_total' => round($afterDiscountTotal, 2),
            'grand_total' => round($grandTotal, 2),
            'refundable' => [
                'enabled' => $refundableRate > 0,
                'name' => $refundableName,
                'rate' => round($refundableRate, 4),
                'amount' => round($refundableAmount, 2),
            ],
            'amount_paid_now' => round($amountPaidNow, 2),
            'remaining_due' => round($remainingDue, 2),
        ];
    }

    private function normalizeStoredCartItems($rawCartItems): array
    {
        if (is_array($rawCartItems)) {
            return $rawCartItems;
        }

        if (!is_string($rawCartItems) || trim($rawCartItems) === '') {
            return [];
        }

        $decoded = json_decode($rawCartItems, true);
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function generateTicketQrCode(): string
    {
        do {
            $ticketCode = 'CVT-' . strtoupper(Str::random(12));
        } while (Transaction::where('ticket_qr_code', $ticketCode)->exists());

        return $ticketCode;
    }

    private function generateConfirmationNumber(): string
    {
        do {
            $confirmationNumber = '8' . str_pad((string) random_int(0, 9999999999), 10, '0', STR_PAD_LEFT);
        } while (Transaction::where('transaction_id', $confirmationNumber)->exists());

        return $confirmationNumber;
    }

    private function buildTicketQrImageUrl(string $ticketCode): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . urlencode($ticketCode);
    }

    private function resolveAffiliateFromRequest(Request $request): ?affiliate
    {
        $websiteId = (int) $request->input('website_id');

        if ($request->filled('affiliate_slug')) {
            $affiliate = Affiliate::where('slug', $request->input('affiliate_slug'))
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if ($affiliate && $this->affiliateAllowedForWebsite($affiliate->id, $websiteId)) {
                return $affiliate;
            }

            return null;
        }

        if (session()->has('affiliate_referral_id')) {
            $affiliate = Affiliate::where('id', session('affiliate_referral_id'))
                ->where('status', 'approved')
                ->where('is_active', true)
                ->first();

            if ($affiliate && $this->affiliateAllowedForWebsite($affiliate->id, $websiteId)) {
                return $affiliate;
            }

            session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
        }

        return null;
    }

    private function affiliateAllowedForWebsite(int $affiliateId, int $websiteId): bool
    {
        if ($affiliateId <= 0 || $websiteId <= 0) {
            return false;
        }

        return AffiliateWebsite::where('affiliate_id', $affiliateId)
            ->where('website_id', $websiteId)
            ->where('is_active', true)
            ->exists();
    }

    private function resolveEntertainerFromRequest(Request $request): ?Entertainer
    {
        $websiteId = (int) $request->input('website_id');
        if ($websiteId <= 0) {
            return null;
        }

        $slug = null;
        if ($request->filled('affiliate_slug')) {
            $slug = (string) $request->input('affiliate_slug');
        } elseif ($request->filled('entertainer_slug')) {
            $slug = (string) $request->input('entertainer_slug');
        }

        if (!$slug) {
            return null;
        }

        return Entertainer::where('slug', $slug)
            ->where('website_id', $websiteId)
            ->where('status', 'approved')
            ->where('is_active', true)
            ->first();
    }

    private function resolveValidatedPromoForCheckout(Request $request, ?Website $website = null): array
    {
        $promoCodeId = (int) $request->input('promo_code');
        if ($promoCodeId <= 0) {
            return [null, 0];
        }

        $websiteId = (int) ($website->id ?? $request->input('website_id'));
        if ($websiteId <= 0) {
            return [null, 0];
        }

        $source = $this->resolvePromoAudienceFromCheckoutRequest($request);

        $promo = PromoCode::query()
            ->where('id', $promoCodeId)
            ->where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->where('audience', $source)
            ->when($source === PromoCode::AUDIENCE_AFFILIATE, function ($query) use ($request) {
                $affiliate = $this->resolveAffiliateFromRequest($request);
                if (!$affiliate) {
                    $query->whereRaw('1 = 0');
                    return;
                }

                $query->where('affiliate_id', $affiliate->id)
                    ->whereNull('entertainer_id');
            })
            ->when($source === PromoCode::AUDIENCE_ENTERTAINER, function ($query) use ($request) {
                $entertainer = $this->resolveEntertainerFromRequest($request);
                if (!$entertainer) {
                    $query->whereRaw('1 = 0');
                    return;
                }

                $query->where('entertainer_id', $entertainer->id)
                    ->whereNull('affiliate_id');
            })
            ->when($source === PromoCode::AUDIENCE_CLUB, function ($query) {
                $query->whereNull('affiliate_id')
                    ->whereNull('entertainer_id');
            })
            ->first();

        if (!$promo) {
            return [null, 0];
        }

        $this->validatePromoConstraintsForCheckout($promo, $request);

        // Discount must be calculated only from package subtotal (packages + addons), excluding all extra charges.
        $cartItems = $this->extractCartItemsFromRequest($request);
        $baseAmount = $this->calculateCartItemsSubtotal($cartItems);

        $discountType = $promo->discount_value_type ?: ($promo->type ?: PromoCode::DISCOUNT_TYPE_PERCENTAGE);
        $rawAmount = isset($promo->discount_value) ? (float) $promo->discount_value : (float) ($promo->percentage ?? 0);
        $discount = $discountType === PromoCode::DISCOUNT_TYPE_FIXED
            ? $rawAmount
            : ($baseAmount * $rawAmount / 100);

        $discount = round(min(max($discount, 0), $baseAmount), 2);

        return [$promo->id, $discount];
    }

    private function resolvePromoAudienceFromCheckoutRequest(Request $request): string
    {
        if ($this->resolveAffiliateFromRequest($request)) {
            return PromoCode::AUDIENCE_AFFILIATE;
        }

        if ($this->resolveEntertainerFromRequest($request)) {
            return PromoCode::AUDIENCE_ENTERTAINER;
        }

        return PromoCode::AUDIENCE_CLUB;
    }

    private function validatePromoConstraintsForCheckout(PromoCode $promo, Request $request): void
    {
        if ((int) ($promo->is_archieved ?? 0) !== 0 || (isset($promo->is_active) && !$promo->is_active)) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code is no longer active.',
            ]);
        }

        $now = now();
        if ($promo->starts_at && $promo->starts_at->gt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code is not active yet.',
            ]);
        }

        if ($promo->ends_at && $promo->ends_at->lt($now)) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code has expired.',
            ]);
        }

        if (!empty($promo->usage_limit_total) && (int) ($promo->usage_count ?? 0) >= (int) $promo->usage_limit_total) {
            throw ValidationException::withMessages([
                'promo_code' => 'This promo code has reached its usage limit.',
            ]);
        }

        $cartItems = $this->extractCartItemsFromRequest($request);
        $packageIds = collect($cartItems)->map(fn ($item) => (int) ($item['package_id'] ?? 0))->filter(fn ($id) => $id > 0)->unique();

        if (($promo->applies_to ?? PromoCode::APPLIES_TO_ALL_PACKAGES) === PromoCode::APPLIES_TO_SPECIFIC_PACKAGES) {
            $allowedPackageIds = collect((array) ($promo->applies_to_package_ids ?? []))
                ->map(fn ($id) => (int) $id)
                ->filter(fn ($id) => $id > 0)
                ->unique();

            if ($allowedPackageIds->isEmpty() || $packageIds->intersect($allowedPackageIds)->isEmpty()) {
                throw ValidationException::withMessages([
                    'promo_code' => 'This promo code does not apply to selected package(s).',
                ]);
            }
        }

        $cartSubtotal = $this->calculateCartItemsSubtotal($cartItems);

        $cartQuantity = collect($cartItems)->sum(fn (array $item) => max(1, (int) ($item['guests'] ?? 1)));
        $minReqType = (string) ($promo->min_requirement_type ?? PromoCode::MIN_REQUIREMENT_NONE);

        if ($minReqType === PromoCode::MIN_REQUIREMENT_AMOUNT) {
            $minAmount = (float) ($promo->min_purchase_amount ?? 0);
            if ($minAmount > 0 && $cartSubtotal < $minAmount) {
                throw ValidationException::withMessages([
                    'promo_code' => 'Minimum order amount for this promo is $' . number_format($minAmount, 2) . '.',
                ]);
            }
        }

        if ($minReqType === PromoCode::MIN_REQUIREMENT_QUANTITY) {
            $minQty = (int) ($promo->min_purchase_quantity ?? 0);
            if ($minQty > 0 && $cartQuantity < $minQty) {
                throw ValidationException::withMessages([
                    'promo_code' => 'Minimum quantity for this promo is ' . $minQty . '.',
                ]);
            }
        }

        if ((bool) ($promo->limit_one_per_customer ?? false)) {
            $email = trim((string) $request->input('package_email', $request->input('reservation_email', '')));
            if ($email !== '' && Transaction::where('promo_code', $promo->id)->where('package_email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'promo_code' => 'This promo code can only be used once per customer.',
                ]);
            }
        }
    }

    private function incrementPromoUsage(?int $promoCodeId): void
    {
        $promoId = (int) $promoCodeId;
        if ($promoId <= 0) {
            return;
        }

        PromoCode::where('id', $promoId)->increment('usage_count');
    }

    private function calculateCartItemsSubtotal(array $cartItems): float
    {
        $subtotal = collect($cartItems)->sum(function (array $item) {
            $guests = max(1, (int) ($item['guests'] ?? 1));
            $isMultiple = $this->isTruthy($item['is_multiple'] ?? false);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $line = $isMultiple ? $unitPrice * $guests : $unitPrice;
            $addons = collect((array) ($item['addons'] ?? []))->sum(fn ($addon) => (float) ($addon['price'] ?? 0));

            return $line + $addons;
        });

        return round(max((float) $subtotal, 0), 2);
    }

    public function downloadPdf($id)
    {
        $transaction = Transaction::with(['website', 'affiliate.user', 'entertainer.user'])->findOrFail($id);

        $this->ensureCanAccess($transaction);

        $html = view('admin.transaction.pdf', compact('transaction'))->render();

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, 'transaction-' . $transaction->id . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function ensureCanAccess(Transaction $transaction)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        if ($user->user_type === 'admin') {
            return;
        }

        if ($user->user_type === 'website_user' && $user->website_id === $transaction->website_id) {
            return;
        }

        if ($user->user_type === 'affiliate' && $user->affiliate_id === $transaction->affiliate_id) {
            return;
        }

        if ($user->user_type === 'entertainer' && $user->entertainer_id === $transaction->entertainer_id) {
            return;
        }

        abort(403);
    }

}
