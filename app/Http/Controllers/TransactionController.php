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
use App\Mail\TransactionMail;
use App\Helpers\PackageLimitHelper;
use Illuminate\Support\Facades\Mail;
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

        $selectedPackage = Package::find($cartSummary['primary_package_id'] ?: $request->input('package_id'));
        $requiresTransportation = $this->cartRequiresTransportation($cartItems, $selectedPackage);

        if ($selectedPackage && $selectedPackage->event_id) {
            $this->ensureEventCapacityAvailable(
                Event::find($selectedPackage->event_id),
                $cartSummary['total_guests']
            );
        }

        // Validate daily package limits
        foreach ($cartItems as $item) {
            $package = Package::find($item['package_id']);
            if ($package) {
                $requestedQuantity = max(1, (int) ($item['guests'] ?? $item['quantity'] ?? 1));
                $result = PackageLimitHelper::canPurchase($package, $requestedQuantity);
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
                            'package_dob' => $add->package_dob,
                            'package_note' => $request->input('package_note'),
                            'transportation_pickup_time' => $request->input('transportation_pickup_time'),
                            'transportation_address' => $request->input('transportation_address'),
                            'transportation_phone' => $request->input('transportation_phone'),
                            'transportation_guest' => $request->input('transportation_guest'),
                            'transportation_note' => $request->input('transportation_note'),
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
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr);
                        $send_mail_club->subject('New Package Purched - ' . $transaction_id);

                        // Purchaser email — full mail with QR
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailData);
                        $send_mail_purchaser->subject('New Package Purched - ' . $transaction_id);

                        $clubEmails = collect($website->emails ?? [])
                            ->pluck('email')
                            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
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
                        //throw $th;
                        // dd($th);
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
                            'package_dob' => $add->package_dob,
                            'package_note' => $request->input('package_note'),
                            'transportation_pickup_time' => $request->input('transportation_pickup_time'),
                            'transportation_address' => $request->input('transportation_address'),
                            'transportation_phone' => $request->input('transportation_phone'),
                            'transportation_guest' => $request->input('transportation_guest'),
                            'transportation_note' => $request->input('transportation_note'),
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
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr);
                        $send_mail_club->subject('New Package Purched - ' . $transaction_id);

                        // Purchaser email — full mail with QR
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailData);
                        $send_mail_purchaser->subject('New Package Purched - ' . $transaction_id);

                        $clubEmails = collect($website->emails ?? [])
                            ->pluck('email')
                            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
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
                        //throw $th;
                        // dd($th);
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

    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            // Admin can see all transactions
            $data = Transaction::with(['event', 'package', 'website', 'affiliate.user', 'entertainer.user'])
                              ->latest()
                              ->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website user can only see their website's transactions
            $data = Transaction::where(function($query) use ($user) {
                                // Direct website_id match
                                $query->where('website_id', $user->website_id)
                                // OR transactions with events from their website
                                ->orWhereHas('event', function($subQuery) use ($user) {
                                    $subQuery->where('website_id', $user->website_id);
                                })
                                // OR transactions with packages from their website
                                ->orWhereHas('package', function($subQuery) use ($user) {
                                    $subQuery->where('website_id', $user->website_id);
                                });
                            })
                            ->with(['event', 'package', 'website', 'affiliate.user'])
                            ->with(['entertainer.user'])
                            ->latest()
                            ->get();
        } else {
            // No access for users without proper permissions
            $data = collect();
        }
        
        return view('admin.transaction.index', compact('data'));
    }

    public function reservation_store($slug, Request $request)
    {
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

            $new = new Transaction;
            $new->transaction_id = null;
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
            $new->event_id = $request->input('event_id');
            $new->website_id = $event != null ? $event->website_id : $request->website_id;
            $new->total = 0; // No payment required for free reservations
            $new->type = 'reservation';
            $new->men = $request->men_count;
            $new->women = $request->women_count;
            $new->save();
            $this->applyReferralCommission($request, $new);

            try {
                        //code...
                        // Prepare all transaction data for the email body
                        $mailData = [
                            'transaction_id' => $transaction_id,
                            'package_first_name' => $request->input('package_first_name'),
                            'package_last_name' => $request->input('package_last_name'),
                            'package_phone' => $request->input('package_phone'),
                            'package_email' => $request->input('package_email'),
                            'package_dob' => $add->package_dob,
                            'package_note' => $request->input('package_note'),
                            'transportation_pickup_time' => $request->input('transportation_pickup_time'),
                            'transportation_address' => $request->input('transportation_address'),
                            'transportation_phone' => $request->input('transportation_phone'),
                            'transportation_guest' => $request->input('transportation_guest'),
                            'transportation_note' => $request->input('transportation_note'),
                            'addons' => $request->input('addons'),
                            'package_id' => $request->input('package_id'),
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
                        ];
    
                        $website = Website::findOrFail($request->website_id);
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        $send_mail = new \App\Mail\TransactionMail($mailData);
                        $send_mail->subject('New Package Purched - ' . $transaction_id);
                        // Send the email
                        foreach ($website->emails as $key => $value) {
                            \Illuminate\Support\Facades\Mail::to($value->email)->send($send_mail);
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                        // dd($th);
                    }

            // Redirect to thank you page with transaction details
            return redirect()->route('thank-you')
                ->with('transaction', $new)
                ->with('website', $website)
                ->with('paymentType', 'full')
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
        
        $change->status = $status;
        $change->update();

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

        if ((string) $transaction->type !== 'package' || (string) $transaction->status !== '1') {
            return response()->json([
                'success' => false,
                'message' => 'This transaction is not an active paid package ticket.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'ticket_qr_code' => $transaction->ticket_qr_code,
                'guest_name' => trim(($transaction->package_first_name ?? '') . ' ' . ($transaction->package_last_name ?? '')),
                'package_email' => $transaction->package_email,
                'package_phone' => $transaction->package_phone,
                'website_name' => optional($transaction->website)->name,
                'total' => number_format((float) $transaction->total, 2, '.', ''),
                'package_use_date' => $transaction->package_use_date,
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

        $transaction->checked_in_status = true;
        $transaction->checked_in_at_pacific = Carbon::now('America/Los_Angeles');
        $transaction->checked_in_by_user_id = auth()->id();
        $transaction->save();

        return redirect()->route('admin.transaction.scan')
            ->with('success', 'Check-in completed for ticket #' . $transaction->ticket_qr_code . '.');
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

                return [
                    'id' => $addon['id'] ?? null,
                    'name' => $addon['name'] ?? '',
                    'price' => isset($addon['price']) ? (float) $addon['price'] : 0,
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

    private function ensureEventCapacityAvailable(?Event $event, int $requestedGuests): void
    {
        if (!$event || $event->attendee_limit === null) {
            return;
        }

        $limit = (int) $event->attendee_limit;
        if ($limit <= 0) {
            return;
        }

        $requestedGuests = max(1, $requestedGuests);
        $confirmedAttendees = $this->countConfirmedEventAttendees($event);
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

    private function countConfirmedEventAttendees(Event $event): int
    {
        return Transaction::query()
            ->where('event_id', $event->id)
            ->where('status', 1)
            ->get(['type', 'package_number_of_guest', 'men', 'women'])
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
        $transaction->affiliate_source = $affiliate->slug;
        $transaction->save();

        if ($commissionAmount > 0) {
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
                'description' => 'Commission earned from package purchase #' . $transaction->id,
                'meta' => [
                    'package_id' => $packageId,
                    'website_id' => $transaction->website_id,
                    'commission_percentage' => $commissionPercentage,
                    'commission_base_amount' => round(max($baseAmount, 0), 2),
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
        $transaction->entertainer_source = $entertainer->slug;
        $transaction->save();

        if ($commissionAmount > 0) {
            $newBalance = round((float) $entertainer->wallet_balance + $commissionAmount, 2);
            $entertainer->wallet_balance = $newBalance;
            $entertainer->save();

            EntertainerWalletTransaction::create([
                'entertainer_id' => $entertainer->id,
                'transaction_id' => $transaction->id,
                'type' => 'commission',
                'status' => 'completed',
                'amount' => $commissionAmount,
                'balance_after' => $newBalance,
                'description' => 'Commission earned from package purchase #' . $transaction->id,
                'meta' => [
                    'package_id' => $packageId,
                    'website_id' => $transaction->website_id,
                    'commission_percentage' => $commissionPercentage,
                    'commission_base_amount' => round(max($baseAmount, 0), 2),
                ],
            ]);
        }
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

                    return [
                        'name' => trim((string) ($addon['name'] ?? '')),
                        'price' => (float) ($addon['price'] ?? 0),
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

        $serviceChargeRate = (float) ($website->service_charge_fee ?? 0);
        $serviceChargeName = (string) ($website->service_charge_name ?? 'Service Charge');
        $serviceChargeEnabled = $serviceChargeRate > 0 && trim($serviceChargeName) !== '' && trim($serviceChargeName) !== '0';
        $serviceChargeAmount = $serviceChargeEnabled ? ($itemsSubtotal * $serviceChargeRate / 100) : 0;

        $gratuityRate = (float) ($website->gratuity_fee ?? 0);
        $gratuityName = (string) ($website->gratuity_name ?? 'Gratuity Fee');
        $gratuityEnabled = $gratuityRate > 0 && trim($gratuityName) !== '' && trim($gratuityName) !== '0';
        $gratuityAmount = $gratuityEnabled ? ($itemsSubtotal * $gratuityRate / 100) : 0;

        $salesTaxRate = (float) ($website->sales_tax_fee ?? 0);
        $salesTaxName = (string) ($website->sales_tax_name ?? 'Sales Tax');
        $salesTaxEnabled = $salesTaxRate > 0 && trim($salesTaxName) !== '' && trim($salesTaxName) !== '0';
        $salesTaxBase = $itemsSubtotal + $serviceChargeAmount + $gratuityAmount;
        $salesTaxAmount = $salesTaxEnabled ? ($salesTaxBase * $salesTaxRate / 100) : 0;

        $preDiscountTotal = $itemsSubtotal + $serviceChargeAmount + $gratuityAmount + $salesTaxAmount;

        $promoDiscount = max(0, (float) ($transaction->discounted_amount ?? 0));
        if ($promoDiscount > $preDiscountTotal) {
            $promoDiscount = $preDiscountTotal;
        }

        $afterDiscountTotal = max($preDiscountTotal - $promoDiscount, 0);

        $processingFeeRate = (float) ($website->processing_fee ?? 0);
        $processingFeeType = strtolower((string) ($website->processing_fee_type ?? 'percentage'));
        if ($processingFeeType !== 'flat') {
            $processingFeeType = 'percentage';
        }

        $processingFeeAmount = 0;
        if ($processingFeeRate > 0) {
            $processingFeeAmount = $processingFeeType === 'flat'
                ? $processingFeeRate
                : ($afterDiscountTotal * $processingFeeRate / 100);
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
            'pre_discount_total' => round($preDiscountTotal, 2),
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

    private function buildTicketQrImageUrl(string $ticketCode): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=' . urlencode($ticketCode);
    }

    private function resolveAffiliateFromRequest(Request $request): ?Affiliate
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

        $baseAmount = max((float) $request->input('payment_total', $request->input('total', 0)), 0);
        $rawAmount = (float) ($promo->percentage ?? 0);
        $discount = $promo->type === 'fixed'
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

}
