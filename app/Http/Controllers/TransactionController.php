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
use App\Mail\TransactionMail;
use Illuminate\Support\Facades\Mail;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Stripe;

class TransactionController extends Controller
{
    public function store($slug, Request $request)
    {
        // dd($request->all());

        $selectedPackage = Package::find($request->input('package_id'));
        $requiresTransportation = $selectedPackage
            && ($selectedPackage->transportation == 1 || $selectedPackage->transportation === true || $selectedPackage->transportation === '1');

        if ($requiresTransportation) {
            $request->validate(
                [
                    'transportation_phone' => ['required', 'string', 'max:50'],
                ],
                [
                    'transportation_phone.required' => 'Contact Phone Number or WhatsApp is required for transportation packages.',
                ]
            );
        }

        $setting = Setting::find(1);

        $w = Website::find($request->website_id);

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
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $request->input('package_number_of_guest');
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
                    $add->promo_code = $request->input('promo_code');
                    $add->actual_total = $request->input('payment_total');
                    $add->discounted_amount = $request->input('discounted_amount');
                    $add->transportation_pickup_time = $request->input('transportation_pickup_time');
                    $add->transportation_address = $request->input('transportation_address');
                    $add->transportation_phone = $request->input('transportation_phone');
                    $add->transportation_guest = $request->input('transportation_guest');
                    $add->transportation_note = $request->input('transportation_note');
                    $add->addons = $request->input('addons');
                    $add->package_id = $request->input('package_id');
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
    
    
                    $event_id = Package::find($request->input('package_id'))->event_id;
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $request->input('addons');
                    $add->type = 'package';
                    $add->save();
                    $this->applyAffiliateCommission($request, $add);
    
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
    
                        $website = Website::findOrFail($website_id);
    
                        // Dynamically set SMTP config from $website
                        if ($website->smtp->host && $website->smtp->port && $website->smtp->username && $website->smtp->password && $website->smtp->encryption) {
                            config([
                                'mail.mailers.smtp.host' => $website->smtp->host,
                                'mail.mailers.smtp.port' => $website->smtp->port,
                                'mail.mailers.smtp.username' => $website->smtp->username,
                                'mail.mailers.smtp.password' => $website->smtp->password,
                                'mail.mailers.smtp.encryption' => $website->smtp->encryption,
                                'mail.from.address' => $website->smtp->from_email ?? $website->email,
                                'mail.from.name' => $website->smtp->from_name ?? $website->name,
                            ]);
                            // dd(config('mail'));
                        }
    
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
                        ->with('transaction', $add)
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
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $request->input('package_number_of_guest');
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
                    $add->promo_code = $request->input('promo_code');
                    $add->actual_total = $request->input('payment_total');
                    $add->discounted_amount = $request->input('discounted_amount');
                    $add->transportation_pickup_time = $request->input('transportation_pickup_time');
                    $add->transportation_address = $request->input('transportation_address');
                    $add->transportation_phone = $request->input('transportation_phone');
                    $add->transportation_guest = $request->input('transportation_guest');
                    $add->transportation_note = $request->input('transportation_note');
                    $add->addons = $request->input('addons');
                    $add->package_id = $request->input('package_id');
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
    
    
                    $event_id = Package::find($request->input('package_id'))->event_id;
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $request->input('addons');
                    $add->type = 'package';
                    $add->save();
                    $this->applyAffiliateCommission($request, $add);
    
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
    
                        $website = Website::findOrFail($website_id);
    
                        // Dynamically set SMTP config from $website
                        if ($website->smtp->host && $website->smtp->port && $website->smtp->username && $website->smtp->password && $website->smtp->encryption) {
                            config([
                                'mail.mailers.smtp.host' => $website->smtp->host,
                                'mail.mailers.smtp.port' => $website->smtp->port,
                                'mail.mailers.smtp.username' => $website->smtp->username,
                                'mail.mailers.smtp.password' => $website->smtp->password,
                                'mail.mailers.smtp.encryption' => $website->smtp->encryption,
                                'mail.from.address' => $website->smtp->from_email ?? $website->email,
                                'mail.from.name' => $website->smtp->from_name ?? $website->name,
                            ]);
                            // dd(config('mail'));
                        }
    
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
                        ->with('transaction', $add)
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
            $data = Transaction::with(['event', 'package', 'website', 'affiliate.user'])
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
            $this->applyAffiliateCommission($request, $new);

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
    
                        // Dynamically set SMTP config from $website
                        if ($website->smtp->host && $website->smtp->port && $website->smtp->username && $website->smtp->password && $website->smtp->encryption) {
                            config([
                                'mail.mailers.smtp.host' => $website->smtp->host,
                                'mail.mailers.smtp.port' => $website->smtp->port,
                                'mail.mailers.smtp.username' => $website->smtp->username,
                                'mail.mailers.smtp.password' => $website->smtp->password,
                                'mail.mailers.smtp.encryption' => $website->smtp->encryption,
                                'mail.from.address' => $website->smtp->from_email ?? $website->email,
                                'mail.from.name' => $website->smtp->from_name ?? $website->name,
                            ]);
                            // dd(config('mail'));
                        }
    
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

        return view('thank-you', compact('transaction', 'invoice', 'website', 'paymentType'));
    }

    private function applyAffiliateCommission(Request $request, Transaction $transaction): void
    {
        $affiliate = $this->resolveAffiliateFromRequest($request);
        if (!$affiliate) {
            return;
        }

        $packageId = (int) $request->input('package_id');
        if ($packageId <= 0) {
            $transaction->affiliate_id = $affiliate->id;
            $transaction->affiliate_source = $affiliate->slug;
            $transaction->save();
            session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
            return;
        }

        $mapping = AffiliatePackage::where('affiliate_id', $affiliate->id)
            ->where('package_id', $packageId)
            ->where('is_active', true)
            ->first();

        if (!$mapping) {
            return;
        }

        $commissionPercentage = (float) ($mapping->commission_percentage ?? $affiliate->default_commission_percentage);
        $baseAmount = (float) ($transaction->total ?? 0);
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
                ],
            ]);
        }

        session()->forget(['affiliate_referral_id', 'affiliate_referral_slug']);
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

}
