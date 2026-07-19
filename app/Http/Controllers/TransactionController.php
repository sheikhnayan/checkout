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
    /**
     * Parse a money amount that may arrive with thousands separators, a currency
     * symbol or stray whitespace (e.g. "16,000.00" or "$1,234.50") into a clean
     * float rounded to 2 decimals. CRITICAL: a raw (float) cast of "16,000.00"
     * silently yields 16, so every amount MUST pass through here before charging.
     */
    private function sanitizeAmount($value): float
    {
        if (is_int($value) || is_float($value)) {
            return round((float) $value, 2);
        }
        // Strip everything except digits, dot and minus (removes commas, $, spaces).
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($clean === '' || !is_numeric($clean)) {
            return 0.0;
        }
        return round((float) $clean, 2);
    }

    private function isPhysicalProductEnabled(?Website $website): bool
    {
        return (bool) ($website?->physical_product_enabled ?? false);
    }

    private function assignShippingDetailsToTransaction(Request $request, Transaction $transaction, ?Website $website): void
    {
        if (!$this->isPhysicalProductEnabled($website)) {
            $transaction->shipping_same_as_billing = false;
            $transaction->shipping_first_name = null;
            $transaction->shipping_last_name = null;
            $transaction->shipping_phone = null;
            $transaction->shipping_email = null;
            $transaction->shipping_address = null;
            $transaction->shipping_city = null;
            $transaction->shipping_state = null;
            $transaction->shipping_country = null;
            $transaction->shipping_zip_code = null;
            return;
        }

        $sameAsBilling = $request->boolean('shipping_same_as_billing');

        $transaction->shipping_same_as_billing = $sameAsBilling;
        $transaction->shipping_first_name = $sameAsBilling
            ? $request->input('payment_first_name')
            : $request->input('shipping_first_name');
        $transaction->shipping_last_name = $sameAsBilling
            ? $request->input('payment_last_name')
            : $request->input('shipping_last_name');
        $transaction->shipping_phone = $sameAsBilling
            ? $request->input('payment_phone')
            : $request->input('shipping_phone');
        $transaction->shipping_email = $sameAsBilling
            ? $request->input('payment_email')
            : $request->input('shipping_email');
        $transaction->shipping_address = $sameAsBilling
            ? $request->input('payment_address')
            : $request->input('shipping_address');
        $transaction->shipping_city = $sameAsBilling
            ? $request->input('payment_city')
            : $request->input('shipping_city');
        $transaction->shipping_state = $sameAsBilling
            ? $request->input('payment_state')
            : $request->input('shipping_state');
        $transaction->shipping_country = $sameAsBilling
            ? $request->input('payment_country')
            : $request->input('shipping_country');
        $transaction->shipping_zip_code = $sameAsBilling
            ? $request->input('payment_zip_code')
            : $request->input('shipping_zip_code');
    }

    private function buildShippingMailData(Request $request, ?Website $website): array
    {
        if (!$this->isPhysicalProductEnabled($website)) {
            return [];
        }

        $sameAsBilling = $request->boolean('shipping_same_as_billing');

        return [
            'shipping_same_as_billing' => $sameAsBilling,
            'shipping_first_name' => $sameAsBilling ? $request->input('payment_first_name') : $request->input('shipping_first_name'),
            'shipping_last_name' => $sameAsBilling ? $request->input('payment_last_name') : $request->input('shipping_last_name'),
            'shipping_phone' => $sameAsBilling ? $request->input('payment_phone') : $request->input('shipping_phone'),
            'shipping_email' => $sameAsBilling ? $request->input('payment_email') : $request->input('shipping_email'),
            'shipping_address' => $sameAsBilling ? $request->input('payment_address') : $request->input('shipping_address'),
            'shipping_city' => $sameAsBilling ? $request->input('payment_city') : $request->input('shipping_city'),
            'shipping_state' => $sameAsBilling ? $request->input('payment_state') : $request->input('shipping_state'),
            'shipping_country' => $sameAsBilling ? $request->input('payment_country') : $request->input('shipping_country'),
            'shipping_zip_code' => $sameAsBilling ? $request->input('payment_zip_code') : $request->input('shipping_zip_code'),
        ];
    }

    /**
     * Validate a PAN using the Luhn checksum algorithm.
     */
    private function passesLuhnCheck(string $cardNumber): bool
    {
        if ($cardNumber === '' || preg_match('/\D/', $cardNumber)) {
            return false;
        }

        $sum = 0;
        $alternate = false;

        for ($i = strlen($cardNumber) - 1; $i >= 0; $i--) {
            $n = (int) $cardNumber[$i];
            if ($alternate) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alternate = !$alternate;
        }

        return ($sum % 10) === 0;
    }

    /**
     * Extract last-4 digits from a masked account number like XXXX1111.
     */
    private function extractLast4FromMaskedAccount(?string $maskedAccountNumber): ?string
    {
        $digits = preg_replace('/\D/', '', (string) ($maskedAccountNumber ?? ''));
        if ($digits === null || strlen($digits) < 4) {
            return null;
        }

        return substr($digits, -4);
    }

    /**
     * Read card metadata from a Stripe charge response.
     */
    private function extractStripeCardMeta($charge): array
    {
        $cardLast4 = null;
        $cardBrand = null;

        if (!$charge) {
            return [$cardLast4, $cardBrand];
        }

        $paymentMethodDetails = $charge->payment_method_details ?? null;
        $paymentMethodCard = $paymentMethodDetails->card ?? null;
        if ($paymentMethodCard) {
            $cardLast4 = (string) ($paymentMethodCard->last4 ?? '');
            $cardBrand = (string) ($paymentMethodCard->brand ?? '');
        }

        if ($cardLast4 === '' || $cardLast4 === null) {
            $source = $charge->source ?? null;
            $cardLast4 = (string) ($source->last4 ?? '');
            if ($cardBrand === '' || $cardBrand === null) {
                $cardBrand = (string) ($source->brand ?? '');
            }
        }

        $cardLast4 = trim((string) $cardLast4);
        $cardBrand = trim((string) $cardBrand);

        return [
            $cardLast4 !== '' ? $cardLast4 : null,
            $cardBrand !== '' ? $cardBrand : null,
        ];
    }

    /**
     * Only tag a package transaction with event_id when checkout was opened
     * from explicit event context (event query/referrer), not from general checkout.
     */
    private function resolvePackageTransactionEventId(Request $request, ?Package $selectedPackage): ?int
    {
        $packageEventId = (int) optional($selectedPackage)->event_id;
        if ($packageEventId <= 0) {
            return null;
        }

        $requestEventId = (int) $request->input('event_id');
        if ($requestEventId > 0 && $requestEventId === $packageEventId) {
            return $packageEventId;
        }

        $requestedEventName = trim((string) $request->input('event_name', ''));
        if ($requestedEventName !== '') {
            $matchedEventId = (int) Event::query()
                ->where('website_id', (int) optional($selectedPackage)->website_id)
                ->where('name', $requestedEventName)
                ->value('id');

            if ($matchedEventId > 0 && $matchedEventId === $packageEventId) {
                return $packageEventId;
            }
        }

        $referrer = (string) $request->headers->get('referer', '');
        if ($referrer !== '') {
            $query = parse_url($referrer, PHP_URL_QUERY);
            if (is_string($query) && $query !== '') {
                parse_str($query, $queryParams);
                $referrerEventName = trim((string) ($queryParams['event_name'] ?? ''));

                if ($referrerEventName !== '') {
                    $matchedReferrerEventId = (int) Event::query()
                        ->where('website_id', (int) optional($selectedPackage)->website_id)
                        ->where('name', $referrerEventName)
                        ->value('id');

                    if ($matchedReferrerEventId > 0 && $matchedReferrerEventId === $packageEventId) {
                        return $packageEventId;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Resolve the Authorize.Net environment URL, honoring the per-website sandbox
     * toggle, then the global setting, defaulting to sandbox when neither is
     * configured (same precedence used by CustomInvoiceController).
     */
    private function authorizeNetEnvironment($website, $setting, bool $usesGlobalKeys = false): string
    {
        // The environment must match the keys being used. A club on the GLOBAL
        // keys obeys the GLOBAL sandbox toggle; a club on its OWN keys obeys its
        // own per-website toggle (falling back to global, then sandbox).
        if ($usesGlobalKeys) {
            $useSandbox = $setting->sandbox_mode ?? null;
        } else {
            $useSandbox = $website->sandbox_mode ?? null;
            if ($useSandbox === null) {
                $useSandbox = $setting->sandbox_mode ?? null;
            }
        }
        if ($useSandbox === null) {
            $useSandbox = true; // safe default: sandbox
        }

        return $useSandbox
            ? \net\authorize\api\constants\ANetEnvironment::SANDBOX
            : \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
    }

    /**
     * Interpret an Authorize.Net createTransaction response into a normalized
     * outcome so the checkout flow does not have to re-derive the two-layer
     * response semantics each time.
     *
     * responseCode: 1 = approved, 2 = declined, 3 = error, 4 = held for review.
     * Both 1 and 4 mean the card WAS charged/authorized — 4 is held by the Fraud
     * Detection Suite and settles on approval, so it must be treated as a
     * (pending) success, never as a failure (that is how double charges happen).
     */
    private function interpretAuthorizeNetResponse($response): array
    {
        $out = [
            'ok' => false,            // true when money was taken (approved or held)
            'held' => false,          // true for responseCode 4 (under review)
            'response_code' => null,  // '1'..'4'
            'trans_id' => null,
            'avs' => null,
            'cvv' => null,
            'account_number' => null,
            'account_type' => null,
            'card_last4' => null,
            'message' => 'Payment could not be processed. You have not been charged.',
        ];

        if ($response === null) {
            return $out;
        }

        $tresponse = $response->getTransactionResponse();

        if ($tresponse === null) {
            // No transactionResponse -> request-level failure (bad credentials,
            // malformed request, etc.). Use the top-level message if present.
            $messages = $response->getMessages();
            if ($messages !== null && $messages->getMessage() !== null && count($messages->getMessage()) > 0) {
                $out['message'] = $messages->getMessage()[0]->getText();
            }
            return $out;
        }

        $code = (string) $tresponse->getResponseCode();
        $out['response_code'] = $code;
        $out['trans_id'] = $tresponse->getTransId();
        $out['avs'] = $tresponse->getAvsResultCode();
        $out['cvv'] = $tresponse->getCvvResultCode();
        $out['account_number'] = $tresponse->getAccountNumber();
        $out['account_type'] = $tresponse->getAccountType();
        $out['card_last4'] = $this->extractLast4FromMaskedAccount($out['account_number']);

        if ($code === '1' || $code === '4') {
            $out['ok'] = true;
            $out['held'] = ($code === '4');
            $tmsgs = $tresponse->getMessages();
            if ($tmsgs !== null && count($tmsgs) > 0) {
                $out['message'] = $tmsgs[0]->getDescription();
            } else {
                $out['message'] = $out['held'] ? 'Your order is being reviewed.' : 'Approved.';
            }
            return $out;
        }

        // Declined (2) or error (3): the real reason lives in the transactionResponse errors.
        $errors = $tresponse->getErrors();
        if ($errors !== null && count($errors) > 0) {
            $out['message'] = $errors[0]->getErrorText();
        }
        return $out;
    }

    public function store($slug, Request $request)
    {

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
        $isSelfDriveTransportation = $requiresTransportation && $request->boolean('transportation_self_drive_ack');
        $requiresArrivalTime = !$requiresTransportation || $isSelfDriveTransportation;

        $this->normalizeTransportationTimeInputs($request, !$isSelfDriveTransportation, $requiresArrivalTime);

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

            $transportationValidationRules = [
                'package_use_date' => ['required', 'date'],
            ];

            if (!$isSelfDriveTransportation) {
                $transportationValidationRules['transportation_pickup_time'] = ['required', 'string', 'max:100'];
                $transportationValidationRules['transportation_address'] = ['required', 'string', 'max:255'];
                $transportationValidationRules['transportation_phone'] = ['required', 'string', 'max:50'];
            }

            $request->validate(
                $transportationValidationRules,
                [
                    'package_use_date.required' => 'Pickup date is required for transportation packages.',
                    'package_use_date.date' => 'Pickup date must be a valid date.',
                    'transportation_pickup_time.required' => 'Pickup time is required for transportation packages.',
                    'transportation_address.required' => 'Pickup location is required for transportation packages.',
                    'transportation_phone.required' => 'Contact Phone Number or WhatsApp is required for transportation packages.',
                ]
            );

            $scheduleWebsite = Website::find($request->website_id);
            if ($scheduleWebsite && !$isSelfDriveTransportation) {
                $this->validateTransportationAvailability($scheduleWebsite, $request, $selectedPackage);
            }
        }

        if ($requiresArrivalTime) {
            $request->validate(
                [
                    'transportation_arrival_time' => ['required', 'string', 'max:100'],
                ],
                [
                    'transportation_arrival_time.required' => 'Time of arrival is required for self-drive or non-transportation packages.',
                ]
            );
        }

        $checkoutWebsite = Website::find($request->website_id);
        $isPhysicalProductCheckout = $this->isPhysicalProductEnabled($checkoutWebsite);
        if ($isPhysicalProductCheckout) {
            $request->validate([
                'shipping_same_as_billing' => ['nullable', 'boolean'],
                'shipping_first_name' => ['nullable', 'string', 'max:100'],
                'shipping_last_name' => ['nullable', 'string', 'max:100'],
                'shipping_phone' => ['nullable', 'string', 'max:50'],
                'shipping_email' => ['nullable', 'email', 'max:255'],
                'shipping_address' => ['nullable', 'string', 'max:255'],
                'shipping_city' => ['nullable', 'string', 'max:120'],
                'shipping_state' => ['nullable', 'string', 'max:120'],
                'shipping_country' => ['nullable', 'string', 'max:120'],
                'shipping_zip_code' => ['nullable', 'string', 'max:30'],
            ]);
        }

        $setting = Setting::find(1);

        $w = Website::find($request->website_id);
        [$validatedPromoCodeId, $validatedDiscountAmount] = $this->resolveValidatedPromoForCheckout($request, $w);
        $amount = $this->sanitizeAmount($request->total);

        if ($amount < 0) {
            return back()->with('error', 'Invalid order amount. You have not been charged. Please refresh the page and try again.');
        }

        // Idempotency guard: stop a rapid double-submit / refresh-resubmit from
        // charging the card twice. Keyed on the order signature and held briefly;
        // it auto-expires so a legitimate retry (e.g. after a decline) still works.
        $idempotencyKey = 'checkout_lock:' . md5(
            $request->website_id . '|'
            . strtolower((string) $request->input('package_email')) . '|'
            . $this->sanitizeAmount($request->total) . '|'
            . json_encode($request->input('cart_items'))
        );
        if (! \Illuminate\Support\Facades\Cache::add($idempotencyKey, 1, 20)) {
            return back()->with('error', 'Your previous order is still being processed. Please wait a few seconds before trying again.');
        }

        if ($amount == 0.0) {
            return $this->completeZeroAmountPackageCheckout(
                $request,
                $cartItems,
                $cartSummary,
                $selectedPackage,
                $validatedPromoCodeId,
                $validatedDiscountAmount
            );
        }

        if ($w->payment_method == 'stripe') {
            # code...

            $w = $checkoutWebsite ?: Website::find($request->website_id);

            if ($w->stripe_secret_key != null) {
                # code...
                $secret = $w->stripe_secret_key;
            }else{
                $secret = $setting->stripe_secret;
            }

            Stripe\Stripe::setApiKey($secret);

                        // 3️⃣ Create a one‑time token from the raw card data
                        // Sanitize amount ("16,000.00" -> 16000.00, not 16) and send
                        // integer cents to Stripe.
                        $stripeAmount = $amount;

                        $charge = null;
                        if ($stripeAmount > 0) {
                            try {
                                $charge = Stripe\Charge::create([
                                    "amount" => (int) round($stripeAmount * 100),
                                    "currency" => "usd",
                                    "source" => $request->stripeToken,
                                    "description" => "Payment fit"
                                ]);
                            } catch (\Stripe\Exception\CardException $e) {
                                \Log::warning('Stripe card declined', ['website_id' => $request->website_id, 'message' => $e->getMessage()]);
                                return back()->with('error', 'Payment failed: ' . $e->getMessage());
                            } catch (\Throwable $e) {
                                \Log::error('Stripe charge error', ['website_id' => $request->website_id, 'error' => $e->getMessage()]);
                                return back()->with('error', 'We could not process your card. You have NOT been charged. Please try again.');
                            }
                        }

                    $transaction_id = $charge ? $charge->id : ('FREE-' . strtoupper(Str::random(16)));
                    [$stripeCardLast4, $stripeCardBrand] = $this->extractStripeCardMeta($charge);
    
                    $ipAddress = $request->ip();
    
                    $add = new Transaction();
                    $add->transaction_id = $transaction_id;
                    // Stripe charges are captured immediately on success (no held state).
                    $add->payment_status = 'approved';
                    $add->gateway_response_code = $charge ? 'stripe_succeeded' : 'free_checkout';
                    $add->payment_card_last4 = $stripeCardLast4;
                    $add->payment_card_brand = $stripeCardBrand;
                    $add->ticket_qr_code = $this->generateTicketQrCode();
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $cartSummary['total_guests'];
                    $add->package_use_date = $request->input('package_use_date');
                    $add->business_company = $request->input('business_company');
                    $add->business_vat = $request->input('business_vat');
                    $add->business_address = $request->input('business_address');
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
                    $add->transportation_pickup_time = $isSelfDriveTransportation ? null : $request->input('transportation_pickup_time');
                    $add->transportation_arrival_time = $requiresArrivalTime ? $request->input('transportation_arrival_time') : null;
                    $add->transportation_address = $isSelfDriveTransportation ? null : $request->input('transportation_address');
                    $add->transportation_phone = $isSelfDriveTransportation ? null : $request->input('transportation_phone');
                    $add->transportation_guest = $isSelfDriveTransportation ? null : $request->input('transportation_guest');
                    $add->transportation_note = $isSelfDriveTransportation ? null : $request->input('transportation_note');
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
                    $this->assignShippingDetailsToTransaction($request, $add, $w);
    
    
                    $event_id = $this->resolvePackageTransactionEventId($request, $selectedPackage);
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->type = 'package';
                    $add->save();
                    $this->incrementPromoUsage($validatedPromoCodeId);
                    $this->applyReferralCommission($request, $add, (float) ($cartSummary['commission_base_amount'] ?? 0));

                    // ClubLifter: do not send transportation payload when self-drive is selected.
                    if (!$isSelfDriveTransportation) {
                        $this->sendClubLifterScheduleAfterResponse($add);
                    }
    
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
                            'transportation_pickup_time' => $add->transportation_pickup_time,
                            'transportation_arrival_time' => $add->transportation_arrival_time,
                            'transportation_mode' => $requiresTransportation
                                ? ($isSelfDriveTransportation ? 'Self Drive Selected' : 'Pickup Requested')
                                : null,
                            'transportation_address' => $add->transportation_address,
                            'transportation_phone' => $add->transportation_phone,
                            'transportation_guest' => $add->transportation_guest,
                            'transportation_note' => $add->transportation_note,
                            'host_name' => $request->input('host_name'),
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
                        $isPhysicalProductWebsite = $this->isPhysicalProductEnabled($website);
                        $mailData['club_name'] = $website->name;
                        $mailData['website_name'] = $website->name;
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
                        $mailData = array_merge($mailData, $this->buildShippingMailData($request, $website));
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr, $add, $cartItems, $mailData['price_breakdown'], $website, false, 'manager');

                        // Purchaser email — hide QR for physical product websites.
                        $mailDataForPurchaser = $isPhysicalProductWebsite ? $mailDataNoQr : $mailData;
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailDataForPurchaser, $add, $cartItems, $mailData['price_breakdown'], $website, !$isPhysicalProductWebsite, 'guest');

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
                        try {
                            $purchaserPhone = $add->package_phone;
                            if ($purchaserPhone) {
                                $smsService = new \App\Services\TelnyxSmsService();
                                $smsData = [
                                    'transaction_id' => $add->transaction_id,
                                    'club_name' => $website->name ?? 'Venue',
                                    'club_slug' => $website->slug ?? '',
                                    'package_name' => $cartSummary['package_name'] ?? 'Package',
                                    'quantity' => $request->input('quantity', 1),
                                    'package_use_date' => $add->package_use_date,
                                    'total_amount' => $add->total,
                                ];
                                $smsService->sendTransactionNotification($purchaserPhone, $smsData, 'package');
                            }
                        } catch (\Exception $e) {
                            // Log but don't crash if SMS fails
                            \Log::error('SMS failed: ' . $e->getMessage());
                        }

                        $this->sendDispatcherBookingSms($website, $add, $cartSummary['package_name'] ?? null);
                    } catch (\Throwable $th) {
                        // The card is already charged and the order saved at this point —
                        // a confirmation-email failure must NOT bounce the customer back to
                        // checkout (which risks a double charge). Log and continue.
                        report($th);
                    }




                    // Redirect to thank you page with transaction details
                    return redirect()->route('thank-you')
                        ->with('transaction', $add->fresh())
                        ->with('website', $website)
                        ->with('paymentType', 'full');



        } else {
            # code...
            $w = $checkoutWebsite ?: Website::find($request->website_id);

            if ($w->authorize_app_key != null) {
                // Club uses its own Authorize.Net account.
                $app = $w->authorize_app_key;
                $secret = $w->authorize_secret_key;
                $usesGlobalKeys = false;
            } else {
                // Club uses the global Authorize.Net account.
                $app = $setting->authorize_key;
                $secret = $setting->authorize_secret;
                $usesGlobalKeys = true;
            }
            

            // Strip spaces/formatting so the gateway gets clean digits. The card
            // number is space-grouped in the UI; the CVV has no input mask, so a
            // stray space could otherwise slip through. Keep them as STRINGS — an
            // int cast would drop a leading-zero CVV such as "012" -> 12.
            $cardNumber = preg_replace('/\D/', '', (string) $request->input('card_number'));
            // Build the expiration as YYYY-MM from the raw month/year digits. Do NOT
            // use Carbon::parse("MM/YY") — it reads e.g. "06/28" as June 28 of the
            // CURRENT year, so the YEAR comes out wrong. A wrong expiration makes the
            // issuer's CVV2 check fail even when the customer's CVV is correct, because
            // CVV2 is derived from card number + expiration + service code.
            $expMonth = str_pad(preg_replace('/\D/', '', (string) $request->input('card_month')), 2, '0', STR_PAD_LEFT);
            $expYear  = preg_replace('/\D/', '', (string) $request->input('card_year'));
            if (strlen($expYear) === 2) {
                $expYear = '20' . $expYear;
            }
            $expirationDate = (strlen($expYear) === 4 && (int) $expMonth >= 1 && (int) $expMonth <= 12)
                ? ($expYear . '-' . $expMonth)
                : null;
            if (empty($expirationDate)) {
                return back()->with('error', 'Invalid card expiration date. You have not been charged. Please re-check your card details and try again.');
            }
            $cvv = preg_replace('/\D/', '', (string) $request->input('card_cvv'));

            // Strong server-side format checks before any gateway call.
            if (strlen($cardNumber) < 12 || strlen($cardNumber) > 19 || !$this->passesLuhnCheck($cardNumber)) {
                return back()->with('error', 'Invalid card number. You have not been charged. Please re-check your card details and try again.');
            }
            if (strlen($cvv) < 3 || strlen($cvv) > 4) {
                return back()->with('error', 'Invalid card security code. You have not been charged. Please re-check your card details and try again.');
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
            $transactionType = 'authCaptureTransaction';
            $gatewayAmount = $amount;
            $transactionRequestType->setTransactionType($transactionType);
            // Send a plain 2-decimal string with no thousands separator.
            $transactionRequestType->setAmount(number_format($gatewayAmount, 2, '.', ''));
            $transactionRequestType->setPayment($payment);

            // Billing address for AVS (Address Verification Service). Without the
            // street + ZIP, Authorize.Net returns AVS code "U", and fraud filters
            // set to reject "unavailable" will decline/hold legitimate cards.
            $billTo = new AnetAPI\CustomerAddressType();
            $billTo->setFirstName((string) $request->input('payment_first_name'));
            $billTo->setLastName((string) $request->input('payment_last_name'));
            $billTo->setAddress((string) $request->input('payment_address'));
            $billTo->setCity((string) $request->input('payment_city'));
            $billTo->setState((string) $request->input('payment_state'));
            $billTo->setZip((string) $request->input('payment_zip_code'));
            $billTo->setCountry((string) $request->input('payment_country'));
            $transactionRequestType->setBillTo($billTo);

            // Extra signals for the Advanced Fraud Detection Suite so legitimate
            // orders are less likely to be held for review.
            $transactionRequestType->setCustomerIP($request->ip());
            if ($request->filled('payment_email')) {
                $customerData = new AnetAPI\CustomerDataType();
                $customerData->setEmail((string) $request->input('payment_email'));
                $transactionRequestType->setCustomer($customerData);
            }

            $requests = new AnetAPI\CreateTransactionRequest();
            $requests->setMerchantAuthentication($merchantAuthentication);
            $requests->setRefId('ref' . uniqid());
            $requests->setTransactionRequest($transactionRequestType);

            $controller = new AnetController\CreateTransactionController($requests);
            try {
                $response = $controller->executeWithApiResponse($this->authorizeNetEnvironment($w, $setting, $usesGlobalKeys));
            } catch (\Throwable $gatewayException) {
                \Log::error('Authorize.Net gateway call failed', [
                    'website_id' => $request->website_id,
                    'error' => $gatewayException->getMessage(),
                ]);
                return back()->with('error', 'We could not reach the payment processor. You have NOT been charged. Please try again in a moment.');
            }

            // Normalize the two-layer Authorize.Net response once. responseCode
            // 1 = approved, 4 = held for review (both took the money), 2 = declined,
            // 3 = error. Log every outcome for disputes/debugging.
            $anet = $this->interpretAuthorizeNetResponse($response);
            \Log::info('Authorize.Net charge result', [
                'website_id' => $request->website_id,
                'requested_amount' => $amount,
                'gateway_amount' => $gatewayAmount,
                'transaction_type' => $transactionType,
                'response_code' => $anet['response_code'],
                'trans_id' => $anet['trans_id'],
                'avs' => $anet['avs'],
                'cvv' => $anet['cvv'],
                'ok' => $anet['ok'],
                'held' => $anet['held'],
            ]);

            if ($response != null) {
                $tresponse = $response->getTransactionResponse();
                if ($anet['ok']) {
                    // Approved (1) or held-for-review (4). In both cases the card
                    // was charged/authorized, so the order MUST be recorded. Held
                    // orders are saved as 'under_review' and settle on approval.
                    $transaction_id = $anet['trans_id'];

                    $ipAddress = $request->ip();

                    $add = new Transaction();
                    $add->transaction_id = $transaction_id;
                    // Gateway outcome (responseCode 4 = held by Fraud Detection Suite -> under_review).
                    $add->payment_status = $anet['held'] ? 'under_review' : 'approved';
                    $add->gateway_response_code = $anet['response_code'];
                    $add->gateway_avs_result = $anet['avs'];
                    $add->gateway_cvv_result = $anet['cvv'];
                    $add->gateway_message = $anet['message'];
                    $add->payment_card_last4 = $anet['card_last4'];
                    $add->payment_card_brand = $anet['account_type'];
                    $add->ticket_qr_code = $this->generateTicketQrCode();
                    $add->package_first_name = $request->input('package_first_name');
                    $add->ip_address = $ipAddress;
                    $add->package_last_name = $request->input('package_last_name');
                    $add->package_phone = $request->input('package_phone');
                    $add->package_email = $request->input('package_email');
                    $add->package_number_of_guest = $cartSummary['total_guests'];
                    $add->package_use_date = $request->input('package_use_date');
                    $add->business_company = $request->input('business_company');
                    $add->business_vat = $request->input('business_vat');
                    $add->business_address = $request->input('business_address');
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
                    $add->transportation_pickup_time = $isSelfDriveTransportation ? null : $request->input('transportation_pickup_time');
                    $add->transportation_arrival_time = $requiresArrivalTime ? $request->input('transportation_arrival_time') : null;
                    $add->transportation_address = $isSelfDriveTransportation ? null : $request->input('transportation_address');
                    $add->transportation_phone = $isSelfDriveTransportation ? null : $request->input('transportation_phone');
                    $add->transportation_guest = $isSelfDriveTransportation ? null : $request->input('transportation_guest');
                    $add->transportation_note = $isSelfDriveTransportation ? null : $request->input('transportation_note');
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
                    $this->assignShippingDetailsToTransaction($request, $add, $w);
    
    
                    $event_id = $this->resolvePackageTransactionEventId($request, $selectedPackage);
                    $website_id = $request->website_id;
    
    
                    $add->event_id = $event_id;
                    $add->website_id = $website_id;
                    $add->total = $request->input('total');
                    $add->addons = $cartSummary['addons_summary'];
                    $add->type = 'package';
                    $add->save();
                    $this->incrementPromoUsage($validatedPromoCodeId);
                    $this->applyReferralCommission($request, $add, (float) ($cartSummary['commission_base_amount'] ?? 0));

                    // ClubLifter: do not send transportation payload when self-drive is selected.
                    if (!$isSelfDriveTransportation) {
                        $this->sendClubLifterScheduleAfterResponse($add);
                    }
    
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
                            'transportation_pickup_time' => $add->transportation_pickup_time,
                            'transportation_arrival_time' => $add->transportation_arrival_time,
                            'transportation_mode' => $requiresTransportation
                                ? ($isSelfDriveTransportation ? 'Self Drive Selected' : 'Pickup Requested')
                                : null,
                            'transportation_address' => $add->transportation_address,
                            'transportation_phone' => $add->transportation_phone,
                            'transportation_guest' => $add->transportation_guest,
                            'transportation_note' => $add->transportation_note,
                            'host_name' => $request->input('host_name'),
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
                        $isPhysicalProductWebsite = $this->isPhysicalProductEnabled($website);
                        $mailData['club_name'] = $website->name;
                        $mailData['website_name'] = $website->name;
                        $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($add->fresh(), $website);
                        $mailData = array_merge($mailData, $this->buildShippingMailData($request, $website));
    
                        $this->applyWebsiteSmtpConfig($website);
    
                        // Club/manager email — no QR code
                        $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
                        $send_mail_club = new \App\Mail\TransactionMail($mailDataNoQr, $add, $cartItems, $mailData['price_breakdown'], $website, false, 'manager');

                        // Purchaser email — hide QR for physical product websites.
                        $mailDataForPurchaser = $isPhysicalProductWebsite ? $mailDataNoQr : $mailData;
                        $send_mail_purchaser = new \App\Mail\TransactionMail($mailDataForPurchaser, $add, $cartItems, $mailData['price_breakdown'], $website, !$isPhysicalProductWebsite, 'guest');

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
                        // The transaction is already saved and the card already charged at this
                        // point — a confirmation-email rendering/delivery problem must NOT bounce
                        // the customer back to checkout (which risks a double charge). Log and continue.
                        report($th);
                    }

                    // ========== SEND SMS NOTIFICATION ==========
                    try {
                        $purchaserPhone = $add->package_phone;
                        if ($purchaserPhone) {
                            $smsService = new \App\Services\TelnyxSmsService();
                            $smsData = [
                                'transaction_id' => $add->transaction_id,
                                'club_name' => $website->name ?? 'Venue',
                                'club_slug' => $website->slug ?? '',
                                'package_name' => $cartSummary['package_name'] ?? 'Package',
                                'quantity' => $request->input('quantity', 1),
                                'package_use_date' => $add->package_use_date,
                                'total_amount' => $add->total,
                            ];
                            $smsService->sendTransactionNotification($purchaserPhone, $smsData, 'package');
                        }
                    } catch (\Exception $e) {
                        // Log but don't crash if SMS fails
                        \Log::error('SMS failed: ' . $e->getMessage());
                    }

                    $this->sendDispatcherBookingSms($website, $add, $cartSummary['package_name'] ?? null);

                    // Redirect to thank you page with transaction details
                    return redirect()->route('thank-you')
                        ->with('transaction', $add->fresh())
                        ->with('website', $website)
                        ->with('paymentType', 'full');
                } else {
                    // Declined (2) or error (3): show the real transactionResponse
                    // reason (NOT the top-level "Successful." message).
                    \Log::warning('Authorize.Net charge not approved', [
                        'website_id' => $request->website_id,
                        'response_code' => $anet['response_code'],
                        'message' => $anet['message'],
                    ]);
                    return back()->with('error', 'Payment failed: ' . $anet['message']);
                }
            } else {
                \Log::error('Authorize.Net returned a null response (no charge made)', [
                    'website_id' => $request->website_id,
                ]);
                return back()->with('error', 'Payment failed: ' . $anet['message']);
            }
        }
        


        // dd($request->all()); // This line is for debugging purposes, you can remove it later
    }

    private function completeZeroAmountPackageCheckout(
        Request $request,
        array $cartItems,
        array $cartSummary,
        ?Package $selectedPackage,
        ?int $validatedPromoCodeId,
        float $validatedDiscountAmount
    ) {
        $transactionId = 'FREE-' . strtoupper(Str::random(16));
        $ipAddress = $request->ip();
        $eventId = $this->resolvePackageTransactionEventId($request, $selectedPackage);
        $websiteId = (int) $request->website_id;
        $website = Website::findOrFail($websiteId);
        $requiresTransportation = $this->cartRequiresTransportation($cartItems, $selectedPackage);
        $isSelfDriveTransportation = $requiresTransportation && $request->boolean('transportation_self_drive_ack');
        $requiresArrivalTime = !$requiresTransportation || $isSelfDriveTransportation;

        $this->normalizeTransportationTimeInputs($request, !$isSelfDriveTransportation, $requiresArrivalTime);

        $transaction = new Transaction();
        $transaction->transaction_id = $transactionId;
        $transaction->payment_status = 'approved';
        $transaction->gateway_response_code = 'free_checkout';
        $transaction->gateway_message = 'Zero-dollar checkout completed without payment gateway processing.';
        $transaction->ticket_qr_code = $this->generateTicketQrCode();
        $transaction->package_first_name = $request->input('package_first_name');
        $transaction->ip_address = $ipAddress;
        $transaction->package_last_name = $request->input('package_last_name');
        $transaction->package_phone = $request->input('package_phone');
        $transaction->package_email = $request->input('package_email');
        $transaction->package_number_of_guest = $cartSummary['total_guests'];
        $transaction->package_use_date = $request->input('package_use_date');
        $transaction->business_company = $request->input('business_company');
        $transaction->business_vat = $request->input('business_vat');
        $transaction->business_address = $request->input('business_address');

        $packageMonth = $request->input('package_month');
        $packageDay = $request->input('package_day');
        $packageYear = $request->input('package_year');
        $transaction->package_dob = ($packageYear && $packageMonth && $packageDay)
            ? sprintf('%04d-%02d-%02d', $packageYear, $packageMonth, $packageDay)
            : null;

        $transaction->package_note = $request->input('package_note');
        $transaction->host_name = $request->input('host_name');
        $transaction->promo_code = $validatedPromoCodeId;
        $transaction->actual_total = $request->input('payment_total');
        $transaction->discounted_amount = $validatedDiscountAmount;
        $transaction->transportation_pickup_time = $isSelfDriveTransportation ? null : $request->input('transportation_pickup_time');
        $transaction->transportation_arrival_time = $requiresArrivalTime ? $request->input('transportation_arrival_time') : null;
        $transaction->transportation_address = $isSelfDriveTransportation ? null : $request->input('transportation_address');
        $transaction->transportation_phone = $isSelfDriveTransportation ? null : $request->input('transportation_phone');
        $transaction->transportation_guest = $isSelfDriveTransportation ? null : $request->input('transportation_guest');
        $transaction->transportation_note = $isSelfDriveTransportation ? null : $request->input('transportation_note');
        $transaction->addons = $cartSummary['addons_summary'];
        $transaction->package_id = $cartSummary['primary_package_id'] ?: $request->input('package_id');
        $transaction->cart_items = !empty($cartItems) ? $cartItems : null;
        $transaction->payment_first_name = $request->input('payment_first_name');
        $transaction->payment_last_name = $request->input('payment_last_name');
        $transaction->payment_phone = $request->input('payment_phone');
        $transaction->payment_email = $request->input('payment_email');
        $transaction->payment_address = $request->input('payment_address');
        $transaction->payment_city = $request->input('payment_city');
        $transaction->payment_state = $request->input('payment_state');
        $transaction->payment_country = $request->input('payment_country');

        $paymentMonth = $request->input('payment_month');
        $paymentDay = $request->input('payment_day');
        $paymentYear = $request->input('payment_year');
        $transaction->payment_dob = ($paymentYear && $paymentMonth && $paymentDay)
            ? sprintf('%04d-%02d-%02d', $paymentYear, $paymentMonth, $paymentDay)
            : null;

        $transaction->payment_zip_code = $request->input('payment_zip_code');
        $this->assignShippingDetailsToTransaction($request, $transaction, $website);
        $transaction->event_id = $eventId;
        $transaction->website_id = $websiteId;
        $transaction->total = $request->input('total');
        $transaction->type = 'package';
        $transaction->save();

        $this->incrementPromoUsage($validatedPromoCodeId);
        $this->applyReferralCommission($request, $transaction, (float) ($cartSummary['commission_base_amount'] ?? 0));
        if (!$isSelfDriveTransportation) {
            $this->sendClubLifterScheduleAfterResponse($transaction);
        }

        try {
            $mailData = [
                'transaction_id' => $transactionId,
                'package_first_name' => $request->input('package_first_name'),
                'package_last_name' => $request->input('package_last_name'),
                'package_phone' => $request->input('package_phone'),
                'package_email' => $request->input('package_email'),
                'package_use_date' => $request->input('package_use_date'),
                'package_dob' => $transaction->package_dob,
                'package_note' => $request->input('package_note'),
                'transportation_pickup_time' => $transaction->transportation_pickup_time,
                'transportation_arrival_time' => $transaction->transportation_arrival_time,
                'transportation_mode' => $requiresTransportation
                    ? ($isSelfDriveTransportation ? 'Self Drive Selected' : 'Pickup Requested')
                    : null,
                'transportation_address' => $transaction->transportation_address,
                'transportation_phone' => $transaction->transportation_phone,
                'transportation_guest' => $transaction->transportation_guest,
                'transportation_note' => $transaction->transportation_note,
                'host_name' => $request->input('host_name'),
                'business_company' => $transaction->business_company,
                'business_vat' => $transaction->business_vat,
                'business_address' => $transaction->business_address,
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
                'payment_dob' => $transaction->payment_dob,
                'payment_zip_code' => $request->input('payment_zip_code'),
                'event_id' => $eventId,
                'website_id' => $websiteId,
                'total' => $request->input('total'),
                'type' => 'package',
                'ticket_qr_code' => $transaction->ticket_qr_code,
                'ticket_qr_image_url' => $this->buildTicketQrImageUrl($transaction->ticket_qr_code),
            ];

            $mailData['club_name'] = $website->name;
            $mailData['website_name'] = $website->name;
            $mailData['price_breakdown'] = $this->buildPackagePriceBreakdown($transaction->fresh(), $website);
            $mailData = array_merge($mailData, $this->buildShippingMailData($request, $website));

            $this->applyWebsiteSmtpConfig($website);

            $mailDataNoQr = array_diff_key($mailData, array_flip(['ticket_qr_code', 'ticket_qr_image_url']));
            $isPhysicalProductWebsite = $this->isPhysicalProductEnabled($website);
            $sendMailClub = new TransactionMail($mailDataNoQr, $transaction, $cartItems, $mailData['price_breakdown'], $website, false, 'manager');
            $mailDataForPurchaser = $isPhysicalProductWebsite ? $mailDataNoQr : $mailData;
            $sendMailPurchaser = new TransactionMail($mailDataForPurchaser, $transaction, $cartItems, $mailData['price_breakdown'], $website, !$isPhysicalProductWebsite, 'guest');

            $clubEmails = collect($website->emails ?? [])
                ->pluck('email')
                ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
                ->push('hello@cartvip.com')
                ->unique()
                ->values();

            foreach ($clubEmails as $clubEmail) {
                Mail::to($clubEmail)->send(clone $sendMailClub);
            }

            $purchaserEmail = $request->input('package_email');
            if ($purchaserEmail && filter_var($purchaserEmail, FILTER_VALIDATE_EMAIL)) {
                Mail::to($purchaserEmail)->send($sendMailPurchaser);
            }
        } catch (\Throwable $exception) {
            report($exception);
        }

        try {
            $purchaserPhone = $transaction->package_phone;
            if ($purchaserPhone) {
                $smsService = new \App\Services\TelnyxSmsService();
                $smsData = [
                    'transaction_id' => $transaction->transaction_id,
                    'club_name' => $website->name ?? 'Venue',
                    'club_slug' => $website->slug ?? '',
                    'package_name' => $cartSummary['package_name'] ?? 'Package',
                    'quantity' => $request->input('quantity', 1),
                    'package_use_date' => $transaction->package_use_date,
                    'total_amount' => $transaction->total,
                ];
                $smsService->sendTransactionNotification($purchaserPhone, $smsData, 'package');
            }
        } catch (\Exception $exception) {
            \Log::error('SMS failed: ' . $exception->getMessage());
        }

        $this->sendDispatcherBookingSms($website, $transaction, $cartSummary['package_name'] ?? null);

        return redirect()->route('thank-you')
            ->with('transaction', $transaction->fresh())
            ->with('website', $website)
            ->with('paymentType', 'full');
    }

    public function test()
    {
        // Local-only mail debug helper. Never expose dd()/test mail in production.
        if (! app()->environment('local')) {
            abort(404);
        }
        try {
            $mailData = [ 'type' => 'package' ];
            $send_mail = new \App\Mail\TransactionMail($mailData);
            $send_mail->subject('Test Mail - ' . now());
            $to = 'nman0171@gmail.com';
            Mail::to($to)->send($send_mail);
            return response('Test mail sent to ' . $to);
        } catch (\Throwable $th) {
            return response('Exception: ' . $th->getMessage(), 500);
        }
    }

    private function sendDispatcherBookingSms(?Website $website, Transaction $transaction, ?string $packageName = null): void
    {
        if (!$website) {
            return;
        }

        $dispatcherPhone = trim((string) ($website->dispatcher_phone ?? ''));
        if ($dispatcherPhone === '') {
            return;
        }

        try {
            $smsService = new \App\Services\TelnyxSmsService();
            $message = $this->buildDispatcherBookingMessage($website, $transaction, $packageName);
            $smsService->sendCustomMessage($dispatcherPhone, $message);
        } catch (\Throwable $e) {
            \Log::error('Dispatcher SMS failed: ' . $e->getMessage(), [
                'website_id' => $website->id,
                'transaction_id' => $transaction->id,
            ]);
        }
    }

    private function buildDispatcherBookingMessage(Website $website, Transaction $transaction, ?string $packageName = null): string
    {
        $timezone = $website->resolved_timezone ?? 'America/Los_Angeles';

        $dateText = 'N/A';
        if (!empty($transaction->package_use_date)) {
            try {
                $dateText = Carbon::parse((string) $transaction->package_use_date, $timezone)->format('M d, Y');
            } catch (\Throwable $e) {
                $dateText = (string) $transaction->package_use_date;
            }
        } elseif ($transaction->created_at) {
            $dateText = $transaction->created_at->copy()->timezone($timezone)->format('M d, Y');
        }

        $pickupOrArrivalRaw = trim((string) ($transaction->transportation_pickup_time ?: $transaction->transportation_arrival_time ?: ''));
        $timeText = $this->formatDispatcherTime($pickupOrArrivalRaw, $timezone);
        if ($timeText === '' && $transaction->created_at) {
            $timeText = $transaction->created_at->copy()->timezone($timezone)->format('h:i A');
        }
        if ($timeText === '') {
            $timeText = 'N/A';
        }

        $name = trim((string) ($transaction->package_first_name ?? '') . ' ' . (string) ($transaction->package_last_name ?? ''));
        if ($name === '') {
            $name = 'N/A';
        }

        $phone = trim((string) ($transaction->package_phone ?? ''));
        if ($phone === '') {
            $phone = 'N/A';
        }

        $resolvedPackageName = trim((string) ($packageName ?? optional($transaction->package)->name ?? ''));
        if ($resolvedPackageName === '') {
            $resolvedPackageName = 'Package';
        }

        $bookingId = $transaction->id ?: ($transaction->transaction_id ?: 'N/A');
        $venue = trim((string) ($website->short_name ?: $website->name ?: 'Venue'));
        $pickupOrArrival = $this->formatDispatcherTime($pickupOrArrivalRaw, $timezone);
        if ($pickupOrArrival === '') {
            $pickupOrArrival = 'N/A';
        }

        $messageParts = [
            'BOOKING',
            $venue,
            '#' . $bookingId,
            trim($dateText . ' ' . $timeText),
            $name,
            $phone,
            $resolvedPackageName,
            'PU: ' . $pickupOrArrival,
        ];

        $location = trim((string) ($transaction->transportation_address ?? ''));
        if ($location !== '') {
            $messageParts[] = 'Location: ' . $location;
        }

        $transportationGuest = trim((string) ($transaction->transportation_guest ?? ''));
        if ($transportationGuest !== '') {
            $messageParts[] = '# Guests: ' . $transportationGuest;
        }

        return implode(' | ', $messageParts);
    }

    private function formatDispatcherTime(string $rawTime, string $timezone): string
    {
        $rawTime = trim($rawTime);
        if ($rawTime === '') {
            return '';
        }

        try {
            return Carbon::parse($rawTime, $timezone)->format('h:i A');
        } catch (\Throwable $e) {
            return $rawTime;
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
        $showArchivedOnly = $request->boolean('archived') && $this->canManageArchivedTransactions($user);

        if ($user->isAdmin()) {
            $query = $showArchivedOnly ? Transaction::onlyArchived() : Transaction::query();
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
        } elseif ($user->isManager()) {
            $ids = $user->accessibleWebsiteIds();
            $query = Transaction::query()->where(function($query) use ($ids) {
                $query->whereIn('website_id', $ids)
                    ->orWhereHas('event', function($subQuery) use ($ids) {
                        $subQuery->whereIn('website_id', $ids);
                    })
                    ->orWhereHas('package', function($subQuery) use ($ids) {
                        $subQuery->whereIn('website_id', $ids);
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
        } elseif (in_array($statusKey, ['pending', 'approved', 'paid', 'reversed'], true)) {
            $query->where(function ($statusQuery) use ($statusKey) {
                $statusQuery->where('affiliate_commission_status', $statusKey)
                    ->orWhere('entertainer_commission_status', $statusKey);
            });
        } elseif (in_array($statusKey, ['n/a', 'na'], true)) {
            $query->where(function ($statusQuery) {
                $statusQuery->where(function ($affiliateStatusQuery) {
                    $affiliateStatusQuery->whereNull('affiliate_commission_status')
                        ->orWhere('affiliate_commission_status', '');
                })->where(function ($entertainerStatusQuery) {
                    $entertainerStatusQuery->whereNull('entertainer_commission_status')
                        ->orWhere('entertainer_commission_status', '');
                });
            });
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
                $query->whereDate('package_use_date', '>', $today->toDateString())
                    ->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'today') {
                $query->whereDate('package_use_date', $today->toDateString())
                    ->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'weekend') {
                $query->whereRaw("DATE(package_use_date) >= ? AND DATE(package_use_date) <= ? AND DAYOFWEEK(package_use_date) IN (6, 7)", [
                    $tomorrow->toDateString(),
                    $endOfWeek->toDateString()
                ])->whereNotIn('status', [0, 2]);
            } elseif ($reservationFilter === 'past') {
                $query->whereDate('package_use_date', '<', $today->toDateString());
            } elseif ($reservationFilter === 'no_show') {
                $query->whereDate('package_use_date', '<', $today->toDateString())
                    ->where('status', 1)
                    ->where(function ($noShowQuery) {
                        $noShowQuery->whereNull('checked_in_status')
                            ->orWhere('checked_in_status', 0);
                    });
            } elseif ($reservationFilter === 'checked_in') {
                $query->where('checked_in_status', 1);
            }
        }

        $transactions = $query
            ->with(['event.website', 'package.website', 'website', 'affiliate.user', 'entertainer.user'])
            ->latest()
            ->get();

        // Attach the canonical, accurate price breakdown to each transaction so the
        // package details modal can show the full charges / total breakdown.
        $transactions->each(function ($transaction) {
            try {
                $transaction->price_breakdown = $transaction->website
                    ? $this->buildPackagePriceBreakdown($transaction, $transaction->website)
                    : null;
            } catch (\Throwable $e) {
                $transaction->price_breakdown = null;
            }
        });

        return $transactions;
    }

    public function reservation_store($slug, Request $request)
    {
        \Log::info('=== RESERVATION CHECKOUT STARTED ===', ['phone' => $request->input('reservation_phone'), 'email' => $request->input('reservation_email'), 'hostname' => $request->input('transportation_guest')]);

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
                        \Log::info('SMS CHECK - Phone value (reservation)', ['phone' => $guestPhone, 'phone_type' => gettype($guestPhone), 'phone_empty' => empty($guestPhone)]);
                        if ($guestPhone) {
                            try {
                                \Log::info('Attempting to send reservation SMS', ['phone' => $guestPhone]);
                                $smsService = new \App\Services\TelnyxSmsService();
                                $smsData = [
                                    'transaction_id' => $new->transaction_id,
                                    'club_name' => $website->name ?? 'Venue',
                                    'club_slug' => $website->slug ?? '',
                                    'reservation_date' => $new->package_use_date,
                                    'men_count' => $new->men ?? $new->package_men ?? 0,
                                    'women_count' => $new->women ?? $new->package_women ?? 0,
                                    'total_amount' => 0,
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

                        $this->sendDispatcherBookingSms($website, $new, 'Reservation');
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

        if (!$this->userHasWebsiteAccessToTransaction($user, $transaction)) {
            abort(403, 'Access denied. You do not have permission to view this transaction.');
        }

        return view('admin.transaction.show', compact('transaction'));
    }

    public function details($id)
    {
        $user = auth()->user();
        $transaction = Transaction::with(['event', 'package'])->findOrFail($id);

        if (!$this->userHasWebsiteAccessToTransaction($user, $transaction)) {
            abort(403, 'Access denied.');
        }

        return response($this->buildDetailsHtml($transaction), 200, ['Content-Type' => 'text/html']);
    }

    /**
     * Build the transaction details HTML (shared by the admin and portal detail modals).
     */
    private function buildDetailsHtml(Transaction $transaction): string
    {
        $affiliateName = $transaction->affiliate ? ($transaction->affiliate->display_name ?: optional($transaction->affiliate->user)->name) : '';
        $entertainerName = $transaction->entertainer ? ($transaction->entertainer->display_name ?: optional($transaction->entertainer->user)->name) : '';
        $esc = static function ($value): string {
            return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
        };

        $money = static function ($value): string {
            return '$' . number_format((float) ($value ?? 0), 2);
        };

        $pickupRaw = trim((string) ($transaction->transportation_pickup_time ?? ''));
        $pickupFormatted = $pickupRaw;
        if ($pickupRaw !== '' && strpos($pickupRaw, ':') !== false && !preg_match('/\b(?:AM|PM)\b/i', $pickupRaw)) {
            $parts = explode(':', $pickupRaw);
            $hours = isset($parts[0]) ? (int) $parts[0] : 0;
            $minutes = isset($parts[1]) ? $parts[1] : '00';
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $hours = $hours % 12 ?: 12;
            $pickupFormatted = sprintf('%02d:%s %s', $hours, $minutes, $ampm);
        }

        $arrivalRaw = trim((string) ($transaction->transportation_arrival_time ?? ''));
        $arrivalFormatted = $arrivalRaw;
        if ($arrivalRaw !== '' && strpos($arrivalRaw, ':') !== false && !preg_match('/\b(?:AM|PM)\b/i', $arrivalRaw)) {
            $parts = explode(':', $arrivalRaw);
            $hours = isset($parts[0]) ? (int) $parts[0] : 0;
            $minutes = isset($parts[1]) ? $parts[1] : '00';
            $ampm = $hours >= 12 ? 'PM' : 'AM';
            $hours = $hours % 12 ?: 12;
            $arrivalFormatted = sprintf('%02d:%s %s', $hours, $minutes, $ampm);
        }

        $status = $transaction->status;
        $statusText = 'Unknown';
        $statusClass = 'txn-status-unknown';
        if ($status == 1 || $status === 'Completed' || $status === 'Approved') {
            $statusText = 'Completed';
            $statusClass = 'txn-status-completed';
        } elseif ($status == 0 || $status === 'Canceled' || $status === '0') {
            $statusText = 'Canceled';
            $statusClass = 'txn-status-canceled';
        } elseif ($status == 2 || $status === 'Refunded') {
            $statusText = 'Refunded';
            $statusClass = 'txn-status-refunded';
        }

        $source = 'Direct';
        if ($affiliateName) {
            $source = 'Promoter - ' . $affiliateName;
        } elseif ($entertainerName) {
            $source = 'Entertainer - ' . $entertainerName;
        }

        $totalCommission = (float) ($transaction->affiliate_commission_amount ?? 0) + (float) ($transaction->entertainer_commission_amount ?? 0);
        $businessInfo = implode(' | ', array_values(array_filter([
            $transaction->business_company ?? null,
            $transaction->business_vat ?? null,
            $transaction->business_address ?? null,
        ], static fn ($v) => trim((string) $v) !== '')));

        $row = static function (string $label, string $value): string {
            return '<div class="txn-detail-row"><span class="txn-detail-label">' . $label . '</span><span class="txn-detail-value">' . $value . '</span></div>';
        };

        $formatDate = static function ($value, string $fallback = 'N/A'): string {
            $raw = trim((string) ($value ?? ''));
            if ($raw === '') {
                return $fallback;
            }

            try {
                return \Carbon\Carbon::parse($raw)->format('m/d/Y');
            } catch (\Throwable $e) {
                return $raw;
            }
        };

        $isReservationType = strtolower(trim((string) ($transaction->type ?? ''))) === 'reservation';
        $requiresTransportation = $this->transactionRequiresTransportation($transaction);
        $hasTransportationDetails =
            trim((string) ($transaction->transportation_pickup_time ?? '')) !== '' ||
            trim((string) ($transaction->transportation_address ?? '')) !== '' ||
            trim((string) ($transaction->transportation_phone ?? '')) !== '' ||
            trim((string) ($transaction->transportation_note ?? '')) !== '';
        $isSelfDriveTransportation = $requiresTransportation && !$hasTransportationDetails;
        $transportMode = !$requiresTransportation
            ? 'Not Required'
            : ($isSelfDriveTransportation ? 'Self Drive Selected' : 'Pickup Requested');
        $guestCount = (int) ($transaction->package_number_of_guest ?? 0);
        $menCount = (int) ($transaction->men ?: $transaction->package_men ?: 0);
        $womenCount = (int) ($transaction->women ?: $transaction->package_women ?: 0);

        if ($isReservationType && $guestCount <= 0) {
            $guestCount = max($menCount + $womenCount, 0);
        }

        $guestDisplay = (string) $guestCount;
        if ($isReservationType) {
            $guestDisplay .= ' (M: ' . $menCount . ', W: ' . $womenCount . ')';
        }

        $dateText = $transaction->created_at
            ? $transaction->created_at->timezone('America/Los_Angeles')->format('M d, Y h:i A')
            : '';

        $html = '';
        $html .= '<style>
            #transactionModal .txn-detail-card { background:#1e293b; border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:12px; margin-bottom:12px; }
            #transactionModal .txn-detail-title { color:#e0e7ff; font-weight:700; margin-bottom:10px; }
            #transactionModal .txn-detail-row { display:flex; justify-content:space-between; gap:14px; font-size:0.85rem; padding:5px 0; border-bottom:1px dashed rgba(255,255,255,0.08); }
            #transactionModal .txn-detail-row:last-child { border-bottom:none; }
            #transactionModal .txn-detail-label { color:#94a3b8; }
            #transactionModal .txn-detail-value { color:#e2e8f0; font-weight:600; text-align:right; }
            #transactionModal .txn-status-pill { padding:4px 10px; border-radius:999px; font-size:0.75rem; font-weight:700; letter-spacing:0.04em; }
            #transactionModal .txn-status-completed { background:rgba(16,185,129,0.2); color:#34d399; }
            #transactionModal .txn-status-canceled { background:rgba(239,68,68,0.2); color:#f87171; }
            #transactionModal .txn-status-refunded { background:rgba(245,158,11,0.2); color:#fbbf24; }
            #transactionModal .txn-status-unknown { background:rgba(107,114,128,0.2); color:#cbd5e1; }
        </style>';

        $html .= '<div class="txn-detail-card">';
        $html .= '<div class="d-flex flex-wrap align-items-center justify-content-between gap-2">';
        $html .= '<div class="txn-detail-title mb-0">Transaction #' . $esc($transaction->transaction_id ?: $transaction->id) . '</div>';
        $html .= '<span class="txn-status-pill ' . $statusClass . '">' . $esc($statusText) . '</span>';
        $html .= '</div>';
        $html .= '<div style="margin-top:8px;color:#94a3b8;font-size:0.82rem;">' . $esc($dateText) . ' | ' . $esc($transaction->website_id) . '</div>';
        $html .= '</div>';

        $html .= '<div class="row g-3">';

        $html .= '<div class="col-md-6"><div class="txn-detail-card"><div class="txn-detail-title">Guest & Reservation</div>';
        $html .= $row('Guest', $esc(trim(($transaction->package_first_name ?? '') . ' ' . ($transaction->package_last_name ?? ''))));
        $html .= $row('Email', $esc($transaction->package_email));
        $html .= $row('Phone', $esc($transaction->package_phone));
        $html .= $row('DOB', $esc($formatDate($transaction->package_dob)));
        $html .= $row('Order Items', $esc($transaction->package_table_label));
        $html .= $row('Date Of Use', $esc($formatDate($transaction->package_use_date)));
        $html .= $row('Guests', $esc($guestDisplay));
        $html .= $row('Notes', $esc($transaction->package_note ?: 'N/A'));
        $html .= '</div></div>';

        $html .= '<div class="col-md-6"><div class="txn-detail-card"><div class="txn-detail-title">Payment & Charges</div>';
        $html .= $row('Payment Name', $esc(trim(($transaction->payment_first_name ?? '') . ' ' . ($transaction->payment_last_name ?? ''))));
        $html .= $row('Payment Email', $esc($transaction->payment_email));
        $html .= $row('Payment Phone', $esc($transaction->payment_phone ?: 'N/A'));
        $html .= $row('Payment Address', $esc(implode(', ', array_values(array_filter([
            $transaction->payment_address ?? null,
            $transaction->payment_city ?? null,
            $transaction->payment_state ?? null,
            $transaction->payment_zip_code ?? null,
        ], static fn ($v) => trim((string) $v) !== '')))));
        $html .= $row('Payment Country', $esc($transaction->payment_country ?: 'N/A'));
        $html .= $row('Card Brand', $esc($transaction->payment_card_brand ?: 'N/A'));
        $html .= $row('Card Last 4', $esc($transaction->payment_card_last4 ?: 'N/A'));
        $html .= $row('Payment DOB', $esc($formatDate($transaction->payment_dob)));
        $html .= $row('Promo Code', $esc($transaction->promo_code ?: 'N/A'));
        $html .= $row('Discounted Amount', $esc($money($transaction->discount ?? 0)));
        $html .= $row('Subtotal', $esc($money($transaction->sub_total ?? 0)));
        $html .= $row('Gratuity', $esc($money($transaction->gratuity ?? 0)));
        $html .= $row('Service Charge', $esc($money($transaction->service_charge ?? 0)));
        $html .= $row('Processing Fee', $esc($money($transaction->processing_fee ?? 0)));
        $html .= $row('Non Refundable Deposit', $esc($money($transaction->refundable ?? 0)));
        $html .= $row('Amount Paid', $esc($money($transaction->total ?? 0)));
        $html .= $row('Amount Due', $esc($money($transaction->due ?? 0)));
        $html .= '</div></div>';

        $html .= '<div class="col-md-6"><div class="txn-detail-card"><div class="txn-detail-title">Source & Fee</div>';
        $html .= $row('Source', $esc($source));
        $html .= $row('Type', $esc($transaction->type ?: 'N/A'));
        $html .= $row('Event ID', $esc($transaction->event_id ?: 'N/A'));
        $html .= $row('Total Fee', $esc($money($totalCommission)));

        if ($affiliateName || ((float) ($transaction->affiliate_commission_amount ?? 0) > 0) || ((float) ($transaction->affiliate_commission_percentage ?? 0) > 0) || $transaction->affiliate_commission_status) {
            $affText = ($affiliateName ?: 'N/A')
                . ' | ' . number_format((float) ($transaction->affiliate_commission_percentage ?? 0), 2) . '%'
                . ' | ' . $money($transaction->affiliate_commission_amount ?? 0)
                . ($transaction->affiliate_commission_status ? (' | ' . strtoupper((string) $transaction->affiliate_commission_status)) : '');
            $html .= $row('Promoter Fee', $esc($affText));
        }

        if ($entertainerName || ((float) ($transaction->entertainer_commission_amount ?? 0) > 0) || ((float) ($transaction->entertainer_commission_percentage ?? 0) > 0) || $transaction->entertainer_commission_status) {
            $entText = ($entertainerName ?: 'N/A')
                . ' | ' . number_format((float) ($transaction->entertainer_commission_percentage ?? 0), 2) . '%'
                . ' | ' . $money($transaction->entertainer_commission_amount ?? 0)
                . ($transaction->entertainer_commission_status ? (' | ' . strtoupper((string) $transaction->entertainer_commission_status)) : '');
            $html .= $row('Entertainer Fee', $esc($entText));
        }

        $html .= $row('IP Address', $esc($transaction->ip_address ?: ''));
        $html .= '</div></div>';

        $html .= '<div class="col-md-6"><div class="txn-detail-card"><div class="txn-detail-title">Transport & Business</div>';
        $html .= $row('Transport Mode', $esc($transportMode));
        $html .= $row('Pickup Time', $esc($pickupFormatted ?: 'N/A'));
        $html .= $row('Arrival Time', $esc($arrivalFormatted ?: 'N/A'));
        $html .= $row('Transport Phone', $esc($transaction->transportation_phone ?: 'N/A'));
        $html .= $row('Transport Address', $esc($transaction->transportation_address ?: 'N/A'));
        $html .= $row('Transport Guest', $esc($transaction->transportation_guest ?: 'N/A'));
        $html .= $row('Transport Note', $esc($transaction->transportation_note ?: 'N/A'));
        $html .= $row('Business Info', $esc($businessInfo !== '' ? $businessInfo : 'N/A'));
        $html .= $row('Business Purpose', $esc($transaction->business_purpose ?: 'N/A'));
        $html .= $row('Terms Accepted', 'Yes');
        $html .= $row('SMS Accepted', 'Yes');
        $html .= '</div></div>';

        $html .= '</div>';

        return $html;
    }

    /**
     * Portal-facing transaction details — affiliate/entertainer may view only their own.
     */
    public function portalDetails($id)
    {
        $user = auth()->user();
        $transaction = Transaction::with(['event', 'package', 'affiliate.user', 'entertainer.user'])->findOrFail($id);

        $ownsViaAffiliate = $user && $user->affiliate && (int) $transaction->affiliate_id === (int) $user->affiliate->id;
        $ownsViaEntertainer = $user && $user->entertainer && (int) $transaction->entertainer_id === (int) $user->entertainer->id;

        if (!$ownsViaAffiliate && !$ownsViaEntertainer) {
            abort(403, 'Access denied.');
        }

        return response($this->buildDetailsHtml($transaction), 200, ['Content-Type' => 'text/html']);
    }

    public function update($id, $status)
    {
        $user = auth()->user();
        $change = Transaction::withArchived()->with(['event', 'package'])->findOrFail($id);

        if (!$this->userHasWebsiteAccessToTransaction($user, $change)) {
            abort(403, 'Access denied. You do not have permission to modify this transaction.');
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

    public function archive($id)
    {
        $this->ensureTransactionArchiver();

        $transaction = Transaction::withArchived()
            ->with(['event', 'package'])
            ->findOrFail($id);

        if ($transaction->archived_at) {
            return back()->with('info', 'Transaction is already archived.');
        }

        $transaction->archived_at = now();
        $transaction->archived_by_user_id = auth()->id();
        $transaction->save();

        return back()->with('success', 'Transaction archived successfully.');
    }

    public function bulkArchive(Request $request)
    {
        $this->ensureTransactionArchiver();

        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['integer'],
        ]);

        $ids = collect($validated['transaction_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        if ($ids->isEmpty()) {
            return back()->with('error', 'No transactions selected.');
        }

        $updated = Transaction::withArchived()
            ->whereIn('id', $ids->all())
            ->whereNull('archived_at')
            ->update([
                'archived_at' => now(),
                'archived_by_user_id' => auth()->id(),
                'updated_at' => now(),
            ]);

        return back()->with('success', $updated . ' transaction(s) archived successfully.');
    }

    public function unarchive($id)
    {
        $this->ensureTransactionArchiver();

        $transaction = Transaction::withArchived()->findOrFail($id);
        if (!$transaction->archived_at) {
            return back()->with('info', 'Transaction is not archived.');
        }

        $transaction->archived_at = null;
        $transaction->archived_by_user_id = null;
        $transaction->save();

        return back()->with('success', 'Transaction unarchived successfully.');
    }

    public function bulkUnarchive(Request $request)
    {
        $this->ensureTransactionArchiver();

        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['integer'],
        ]);

        $ids = collect($validated['transaction_ids'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        if ($ids->isEmpty()) {
            return back()->with('error', 'No transactions selected.');
        }

        $updated = Transaction::withArchived()
            ->whereIn('id', $ids->all())
            ->whereNotNull('archived_at')
            ->update([
                'archived_at' => null,
                'archived_by_user_id' => null,
                'updated_at' => now(),
            ]);

        return back()->with('success', $updated . ' transaction(s) unarchived successfully.');
    }

    private function canManageArchivedTransactions($user): bool
    {
        if (!$user || !$user->isAdmin()) {
            return false;
        }

        return strtolower(trim((string) ($user->email ?? ''))) === 'admin@admin.com';
    }

    private function ensureTransactionArchiver(): void
    {
        $user = auth()->user();

        if (!$this->canManageArchivedTransactions($user)) {
            abort(403, 'Only admin@admin.com can archive transactions.');
        }
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
        $transaction = Transaction::with(['event', 'package'])->findOrFail($transactionId);

        // Only back-office users scoped to this transaction's website may view the photo.
        if (!$this->userHasWebsiteAccessToTransaction(auth()->user(), $transaction)) {
            abort(403, 'Unauthorized access to check-in photos.');
        }

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
        $transaction = Transaction::with(['event', 'package'])->findOrFail($transactionId);

        // Only back-office users scoped to this transaction's website may view ID photos.
        if (!$this->userHasWebsiteAccessToTransaction(auth()->user(), $transaction)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        $transaction = Transaction::with(['event', 'package'])->findOrFail($transactionId);

        // Only back-office users scoped to this transaction's website may view ID photos.
        if (!$this->userHasWebsiteAccessToTransaction(auth()->user(), $transaction)) {
            abort(403, 'Unauthorized access to ID photos.');
        }

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

        // Get men and women counts. Reservations store the gender split in `men`/`women`;
        // packages only store a total. Use `?:` so a phantom/zero column never masks the
        // real value (`??` would keep a stored 0 instead of falling through).
        $menCount = (int) ($transaction->men ?: $transaction->package_men ?: 0);
        $womenCount = (int) ($transaction->women ?: $transaction->package_women ?: 0);

        // Calculate total guests from men + women, or use stored total
        $totalGuests = $menCount + $womenCount;
        if ($totalGuests <= 0) {
            $totalGuests = (int) collect($packageDetails)->sum('guests');
            if ($totalGuests <= 0) {
                $totalGuests = max(1, (int) $transaction->package_number_of_guest);
            }
        }

        // Format use date in PT with time (default to midnight if no time)
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

        $guestName = trim(($transaction->package_first_name ?? '') . ' ' . ($transaction->package_last_name ?? ''));

        return redirect()->route('admin.transaction.scan')
            ->with('success', 'Check-in completed for ticket #' . $transaction->ticket_qr_code . $photoStatus)
            ->with('checked_in_success', true)
            ->with('checked_in_name', $guestName);
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
        $arrivalTime = (string) $request->input('transportation_arrival_time');

        if ($pickupDate !== '' && !($selectedPackage && $selectedPackage->event_id) && !$this->isWebsiteOpenOnDate($website, $pickupDate)) {
            throw ValidationException::withMessages([
                'package_use_date' => 'Selected club is closed on that date.',
            ]);
        }

        if ($pickupTime !== '' && !$this->isWithinWebsiteOperatingHours($website, $pickupTime)) {
            throw ValidationException::withMessages([
                'transportation_pickup_time' => 'Please Enter Valid Pickup Time.',
            ]);
        }

        if ($arrivalTime !== '' && !$this->isWithinWebsiteArrivalHours($website, $arrivalTime)) {
            throw ValidationException::withMessages([
                'transportation_arrival_time' => 'Please Enter Valid Arrival Time.',
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
        $startMinutes = $this->convertTimeStringToMinutes($website->pickup_start_time);
        $endMinutes = $this->convertTimeStringToMinutes($website->pickup_end_time);
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

    private function isWithinWebsiteArrivalHours(Website $website, string $arrivalTime): bool
    {
        $startMinutes = $this->convertTimeStringToMinutes($website->operating_start_time);
        $endMinutes = $this->convertTimeStringToMinutes($website->operating_end_time);
        $arrivalMinutes = $this->convertTimeStringToMinutes($arrivalTime);

        if ($arrivalMinutes === null) {
            return false;
        }

        if ($startMinutes === null || $endMinutes === null) {
            return true;
        }

        if ($endMinutes < $startMinutes) {
            return $arrivalMinutes >= $startMinutes || $arrivalMinutes <= $endMinutes;
        }

        return $arrivalMinutes >= $startMinutes && $arrivalMinutes <= $endMinutes;
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

    private function normalizeTransportationTimeInputs(Request $request, bool $normalizePickup, bool $normalizeArrival): void
    {
        $payload = [];

        if ($normalizePickup) {
            $normalizedPickup = $this->normalizeTimeToNextQuarter((string) $request->input('transportation_pickup_time'));
            if ($normalizedPickup !== null) {
                $payload['transportation_pickup_time'] = $normalizedPickup;
            }
        }

        if ($normalizeArrival) {
            $normalizedArrival = $this->normalizeTimeToNextQuarter((string) $request->input('transportation_arrival_time'));
            if ($normalizedArrival !== null) {
                $payload['transportation_arrival_time'] = $normalizedArrival;
            }
        }

        if ($payload !== []) {
            $request->merge($payload);
        }
    }

    private function normalizeTimeToNextQuarter(?string $time): ?string
    {
        $minutes = $this->convertTimeStringToMinutes($time);
        if ($minutes === null) {
            return null;
        }

        $roundedMinutes = (int) (ceil($minutes / 15) * 15);
        $roundedMinutes = (($roundedMinutes % 1440) + 1440) % 1440;

        $hour = intdiv($roundedMinutes, 60);
        $minute = $roundedMinutes % 60;

        return sprintf('%02d:%02d', $hour, $minute);
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
            $publicTransactionId = trim((string) ($transaction->transaction_id ?? ''));
            if ($publicTransactionId === '') {
                $publicTransactionId = (string) $transaction->id;
            }

            AffiliateWalletTransaction::create([
                'affiliate_id' => $affiliate->id,
                'transaction_id' => $transaction->id,
                'type' => 'commission',
                'status' => 'pending',
                'amount' => $commissionAmount,
                'balance_after' => (float) $affiliate->wallet_balance,
                'description' => 'Commission pending hold period for purchase #' . $publicTransactionId,
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
            $publicTransactionId = trim((string) ($transaction->transaction_id ?? ''));
            if ($publicTransactionId === '') {
                $publicTransactionId = (string) $transaction->id;
            }

            EntertainerWalletTransaction::create([
                'entertainer_id' => $entertainer->id,
                'transaction_id' => $transaction->id,
                'type' => 'commission',
                'status' => 'pending',
                'amount' => $commissionAmount,
                'balance_after' => (float) $entertainer->wallet_balance,
                'description' => 'Commission pending hold period for purchase #' . $publicTransactionId,
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

        $publicTransactionId = trim((string) ($transaction->transaction_id ?? ''));
        if ($publicTransactionId === '') {
            $publicTransactionId = (string) $transaction->id;
        }

        AffiliateWalletTransaction::create([
            'affiliate_id' => $affiliate->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'credited',
            'amount' => $commissionAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission approved after hold period for purchase #' . $publicTransactionId,
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

        $publicTransactionId = trim((string) ($transaction->transaction_id ?? ''));
        if ($publicTransactionId === '') {
            $publicTransactionId = (string) $transaction->id;
        }

        EntertainerWalletTransaction::create([
            'entertainer_id' => $entertainer->id,
            'transaction_id' => $transaction->id,
            'type' => 'commission',
            'status' => 'credited',
            'amount' => $commissionAmount,
            'balance_after' => $newBalance,
            'description' => 'Commission approved after hold period for purchase #' . $publicTransactionId,
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
        if (!$user || (!$user->isAdmin() && !$user->isWebsiteUser() && !$user->isBouncer() && !$user->isManager())) {
            abort(403, 'Access denied.');
        }
    }

    /**
     * Website IDs a transaction is associated with (its own, plus its event's and package's).
     */
    private function transactionWebsiteIds(Transaction $transaction): array
    {
        return array_values(array_unique(array_filter([
            $transaction->website_id !== null ? (int) $transaction->website_id : null,
            $transaction->event ? (int) $transaction->event->website_id : null,
            $transaction->package ? (int) $transaction->package->website_id : null,
        ], fn ($v) => $v !== null)));
    }

    /**
     * Whether a back-office user (admin / website user / bouncer / manager) may access a
     * transaction, scoped to the website(s) they are allowed to manage.
     * Admin → all. Manager → allocated sites. Website user / bouncer → their site.
     */
    private function userHasWebsiteAccessToTransaction($user, Transaction $transaction): bool
    {
        if (!$user) {
            return false;
        }
        if ($user->isAdmin()) {
            return true;
        }
        if (!$user->isWebsiteUser() && !$user->isBouncer() && !$user->isManager()) {
            return false;
        }
        $allowed = $user->accessibleWebsiteIds();
        if (empty($allowed)) {
            return false;
        }
        return (bool) array_intersect($this->transactionWebsiteIds($transaction), $allowed);
    }

    private function scannerTransactionQuery()
    {
        $user = auth()->user();

        $query = Transaction::query()->with(['website', 'event', 'package']);

        // Non-admins are scoped to the website(s) they can access
        // (website user / bouncer → their site, manager → allocated sites).
        if ($user && !$user->isAdmin()) {
            $ids = $user->accessibleWebsiteIds();
            $query->where(function ($scopedQuery) use ($ids) {
                $scopedQuery->whereIn('website_id', $ids)
                    ->orWhereHas('event', function ($eventQuery) use ($ids) {
                        $eventQuery->whereIn('website_id', $ids);
                    })
                    ->orWhereHas('package', function ($packageQuery) use ($ids) {
                        $packageQuery->whereIn('website_id', $ids);
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

    /**
     * For a package transaction that includes transportation, send the booking to ClubLifter.
     * Runs after the HTTP response so it never adds latency to or breaks the checkout flow.
     */
    private function sendClubLifterScheduleAfterResponse(Transaction $add): void
    {
        try {
            if (! $this->shouldSendClubLifterForTransaction($add)) {
                return;
            }

            $payload = $this->buildClubLifterSchedulePayload($add);
            if (empty($payload)) {
                return;
            }

            $txnId = $add->id;
            app()->terminating(function () use ($payload, $txnId) {
                try {
                    $result = app(\App\Services\ClubLifterService::class)->schedule($payload);
                    if (is_array($result) && ! empty($result['customer_id'])) {
                        \Log::info('ClubLifter booking created', [
                            'transaction_id' => $txnId,
                            'clublifter_customer_id' => $result['customer_id'],
                        ]);
                    }
                } catch (\Throwable $e) {
                    \Log::warning('ClubLifter schedule (deferred) failed', [
                        'transaction_id' => $txnId,
                        'error' => $e->getMessage(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            \Log::warning('ClubLifter schedule build failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Build the ClubLifter /schedule payload from a package transaction.
     * Returns null when the transaction is not an eligible transport package
     * (so the caller skips the API call entirely).
     */
    private function buildClubLifterSchedulePayload(Transaction $add): ?array
    {
        if (($add->type ?? null) !== 'package') {
            return null;
        }

        // Only transport packages (a pickup address was provided) go to /schedule.
        $pickupAddress = trim((string) ($add->transportation_address ?? ''));
        if ($pickupAddress === '') {
            return null;
        }

        // pickup_datetime is required and must be exactly MM/DD/YYYY HH:MM AM/PM.
        $pickupDateTime = $this->formatClubLifterDateTime($add->package_use_date, $add->transportation_pickup_time);
        if ($pickupDateTime === null) {
            return null;
        }

        $name = trim((string) ($add->package_first_name ?? '') . ' ' . (string) ($add->package_last_name ?? ''));
        if ($name === '') {
            $name = 'Guest';
        }

        $payload = [
            'customer_name'   => $name,
            'pickup_address'  => $pickupAddress,
            'pickup_datetime' => $pickupDateTime,
        ];

        if (! empty($add->package_phone)) {
            $payload['customer_phone'] = (string) $add->package_phone;
        }

        $extraPhones = [];
        if (! empty($add->transportation_phone) && $add->transportation_phone !== $add->package_phone) {
            $extraPhones[] = (string) $add->transportation_phone;
        }
        if (! empty($extraPhones)) {
            $payload['extra_phones'] = $extraPhones;
        }

        $destination = $add->website_id ? optional(\App\Models\Website::find($add->website_id))->name : null;
        if (! empty($destination)) {
            $payload['destination'] = $destination;
        }

        $packageName = $this->resolveClubLifterPackageName($add);
        if (! empty($packageName)) {
            $payload['package'] = $packageName;
        }

        if (! empty($add->package_number_of_guest)) {
            $payload['guests'] = (int) $add->package_number_of_guest;
        }

        $details = trim((string) ($add->transportation_note ?? ''));
        if ($details === '') {
            $details = trim((string) ($add->package_note ?? ''));
        }
        if ($details !== '') {
            $payload['details'] = $details;
        }

        return $payload;
    }

    private function transactionRequiresTransportation(Transaction $transaction): bool
    {
        $cartItems = is_array($transaction->cart_items) ? $transaction->cart_items : [];
        foreach ($cartItems as $item) {
            if (!is_array($item)) {
                continue;
            }

            if ($this->isTruthy($item['transportation'] ?? ($item['transport'] ?? false))) {
                return true;
            }
        }

        $package = $transaction->relationLoaded('package') ? $transaction->package : null;
        if (!$package && !empty($transaction->package_id)) {
            $package = Package::find($transaction->package_id);
        }

        return $package
            && ($package->transportation == 1 || $package->transportation === true || $package->transportation === '1');
    }

    /** Format a date + time string into ClubLifter's required MM/DD/YYYY HH:MM AM/PM. */
    private function formatClubLifterDateTime($date, $time): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            $dt = $date instanceof \Carbon\Carbon ? $date->copy() : \Carbon\Carbon::parse((string) $date);

            $timeStr = trim((string) $time);
            if ($timeStr !== '') {
                try {
                    $t = \Carbon\Carbon::parse($timeStr);
                    $dt->setTime((int) $t->format('H'), (int) $t->format('i'), 0);
                } catch (\Throwable $e) {
                    // Keep the date's existing time if the pickup time can't be parsed.
                }
            }

            return $dt->format('m/d/Y h:i A');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Resolve a human package name to send to ClubLifter (cart items first, then fallbacks). */
    private function resolveClubLifterPackageName(Transaction $add): ?string
    {
        try {
            $items = $this->normalizeStoredCartItems($add->cart_items);
            $names = [];
            foreach ($items as $item) {
                $n = trim((string) ($item['package_name'] ?? ''));
                if ($n !== '') {
                    $names[] = $n;
                }
            }
            if (! empty($names)) {
                return implode(', ', array_values(array_unique($names)));
            }
        } catch (\Throwable $e) {
            // fall through to fallbacks
        }

        if (! empty($add->package_table_label)) {
            return (string) $add->package_table_label;
        }

        if (! empty($add->package_id)) {
            $package = \App\Models\Package::find($add->package_id);
            if ($package && ! empty($package->name)) {
                return (string) $package->name;
            }
        }

        return null;
    }

    private function shouldSendClubLifterForTransaction(Transaction $transaction): bool
    {
        if (empty($transaction->website_id)) {
            return false;
        }

        $website = $transaction->relationLoaded('website') ? $transaction->website : Website::find($transaction->website_id);

        return (bool) ($website->clublifter_enabled ?? false);
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
        $transaction = Transaction::with(['website', 'event', 'package', 'affiliate.user', 'entertainer.user'])->findOrFail($id);

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

        // Admin and website-scoped back-office users (website user / bouncer / manager).
        if ($this->userHasWebsiteAccessToTransaction($user, $transaction)) {
            return;
        }

        // Affiliates and entertainers may download PDFs for their own transactions.
        if ($user->isAffiliate() && $user->affiliate && (int) $user->affiliate->id === (int) $transaction->affiliate_id) {
            return;
        }

        if ($user->isEntertainer() && $user->entertainer && (int) $user->entertainer->id === (int) $transaction->entertainer_id) {
            return;
        }

        abort(403);
    }

}
