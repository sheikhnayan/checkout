<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Permission;
use App\Models\WebsiteRole;
use App\Models\User;
use App\Models\Email;
use App\Models\SMTP;
use App\Models\PaymentLogo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WebsiteController extends Controller
{
    private const DEFAULT_SUCCESS_PAGE = 'https://app.cartvip.com/thank-you';
    private const DEFAULT_DESCRIPTION_LABEL = 'Description';
    private const DEFAULT_TEXT_DESCRIPTION = 'Plan your night with curated VIP options and seamless booking.';

    private const STOCK_PAYMENT_LOGO_CATALOG = [
        'visa' => [
            'name' => 'Visa',
            'logo' => 'https://img.icons8.com/color/48/000000/visa.png',
        ],
        'mastercard' => [
            'name' => 'Mastercard',
            'logo' => 'https://img.icons8.com/color/48/000000/mastercard-logo.png',
        ],
        'amex' => [
            'name' => 'Amex',
            'logo' => 'https://img.icons8.com/color/48/000000/amex.png',
        ],
        'google_pay' => [
            'name' => 'Google Pay',
            'logo' => 'https://img.icons8.com/color/48/000000/google-pay-india.png',
        ],
        'apple_pay' => [
            'name' => 'Apple Pay',
            'logo' => 'https://img.icons8.com/color/48/000000/apple-pay.png',
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $data = Website::all();
        } else {
            // Non-admins are scoped to the website(s) they can access (manager → allocated sites).
            $data = Website::whereIn('id', $this->currentAccessibleWebsiteIds())->get();
        }

        return view('admin.website.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = Website::where('id',$id)->first();
        
        // Check authorization for website users
        $this->authorizeWebsiteAccess($data->id, 'Access denied. You can only manage your own website.');
        
        $data->is_archieved = 1;
        $data->status = 0;
        $data->save();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = Website::where('id',$id)->first();
        
        // Check authorization for website users
        $this->authorizeWebsiteAccess($data->id, 'Access denied. You can only manage your own website.');
        
        $data->is_archieved = 0;
        $data->status = 1;
        $data->save();

        return back();
    } 

    public function toggleStatus($id)
    {
        $user = auth()->user();
        $data = Website::findOrFail($id);

        // Check authorization for website users
        $this->authorizeWebsiteAccess($data->id, 'Access denied. You can only manage your own website.');

        $data->status = $data->status == 1 ? 0 : 1;
        $data->save();

        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        
        // Only admins can create new websites
        if ($user->isWebsiteUser()) {
            abort(403, 'Access denied. Only administrators can create new websites.');
        }
        
        return view('admin.website.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Live check for the website-admin email field:
     *   blocked = used by another account type (affiliate/entertainer/etc.)
     *   reuse   = already a website admin elsewhere -> reuse their password (hide password field)
     *   new     = not used -> a password is required
     */
    public function checkAdminEmail(Request $request)
    {
        $email = trim((string) $request->query('email', ''));
        if ($email === '') {
            return response()->json(['status' => 'new']);
        }

        if (User::where('email', $email)->where('user_type', '!=', 'website_user')->exists()) {
            return response()->json([
                'status' => 'blocked',
                'message' => 'This email is already registered to another account type and cannot be used as a website admin.',
            ]);
        }

        $excludeWebsiteId = $request->query('website_id');
        $admin = User::where('email', $email)
            ->where('user_type', 'website_user')
            ->when($excludeWebsiteId, fn ($q) => $q->where('website_id', '!=', $excludeWebsiteId))
            ->first();

        if ($admin) {
            return response()->json(['status' => 'reuse', 'name' => $admin->name]);
        }

        return response()->json(['status' => 'new']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'hero_badge_1_label' => 'nullable|string|max:80',
            'hero_badge_1_sub' => 'nullable|string|max:120',
            'hero_badge_2_label' => 'nullable|string|max:80',
            'hero_badge_2_sub' => 'nullable|string|max:120',
            'package_section_title' => 'nullable|string|max:120',
            'package_section_subtext' => 'nullable|string|max:255',
            'guest_tab_subtitle' => 'nullable|string|max:120',
            'package_tab_subtitle' => 'nullable|string|max:120',
            'website_admin_name' => 'required|string|max:255',
            'website_admin_email' => 'required|email|max:255',
            'website_admin_password' => 'nullable|string|min:8|confirmed',
            'payment_methods' => 'nullable|array',
            'payment_methods.*' => 'in:visa,mastercard,amex,google_pay,apple_pay',
            'operating_days' => 'nullable|array',
            'operating_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'operating_start_time' => 'nullable|date_format:H:i',
            'operating_end_time' => 'nullable|date_format:H:i',
            'pickup_start_time' => 'nullable|date_format:H:i',
            'pickup_end_time' => 'nullable|date_format:H:i',
            'entertainer_submission_emails' => 'nullable|string',
            'clublifter_enabled' => 'nullable|boolean',
        ]);

        // Website admins (user_type = website_user) may reuse an email across websites; block
        // emails already used by any other account type, and reuse the existing password hash.
        $adminEmail = $request->website_admin_email;
        if (User::where('email', $adminEmail)->where('user_type', '!=', 'website_user')->exists()) {
            return back()->withInput()->withErrors([
                'website_admin_email' => 'This email is already registered to another account type and cannot be used as a website admin.',
            ]);
        }
        $existingWebsiteAdmin = User::where('email', $adminEmail)->where('user_type', 'website_user')->first();
        if (!$existingWebsiteAdmin && !$request->filled('website_admin_password')) {
            return back()->withInput()->withErrors([
                'website_admin_password' => 'A password is required for a new website admin.',
            ]);
        }
        $adminPasswordHash = $existingWebsiteAdmin ? $existingWebsiteAdmin->password : Hash::make($request->website_admin_password);

        Permission::syncFromAdminRoutes();

        $emails = $this->normalizeNotificationEmails($request->input('emails', '[]'));
        $entertainerSubmissionEmails = $this->normalizeNotificationEmails($request->input('entertainer_submission_emails', '[]'));

        $add = new Website;
        $add->name = $request->name;
        $add->domain = $request->domain;
        $add->status = '1';
        
        // Generate slug from name or use provided slug
        if ($request->slug) {
            $add->slug = Website::generateSlug($request->slug);
        } else {
            $add->slug = Website::generateSlug($request->name);
        }
        
        $add->payment_method = $request->input('payment_method', 'authorize');
        $add->lat = $request->lat;
        $add->long = $request->long;
        $add->location = $request->location;
        $add->policy = $request->policy;
        $add->terms = $request->terms;
        $add->success_page = self::DEFAULT_SUCCESS_PAGE;
        $add->text_description = self::DEFAULT_TEXT_DESCRIPTION;
        $add->secondary_description = $request->secondary_description;
        $add->description_label = self::DEFAULT_DESCRIPTION_LABEL;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->hero_badge_1_label = $request->hero_badge_1_label;
        $add->hero_badge_1_sub = $request->hero_badge_1_sub;
        $add->hero_badge_2_label = $request->hero_badge_2_label;
        $add->hero_badge_2_sub = $request->hero_badge_2_sub;
        $add->package_section_title = $request->package_section_title;
        $add->package_section_subtext = $request->package_section_subtext;
        $add->phone = $request->phone;
        $add->reservation = $request->reservation;
        $add->email = $request->email;
        $add->entertainer_submission_emails = $entertainerSubmissionEmails;
        $add->clublifter_enabled = $request->boolean('clublifter_enabled');
        $add->gratuity_fee = $request->gratuity_fee;
        $add->gratuity_name = $request->gratuity_name;
        $add->refundable_fee = $request->refundable_fee;
        $add->refundable_name = $request->refundable_name;
        $add->sales_tax_fee = $request->sales_tax_fee;
        $add->sales_tax_name = $request->sales_tax_name;
        $add->service_charge_fee = $request->service_charge_fee;
        $add->service_charge_name = $request->service_charge_name;
        $add->promo_code_name = $request->promo_code_name;
        $add->description = $request->description;
        $add->stripe_app_key = $request->stripe_app_key;
        $add->stripe_secret_key = $request->stripe_secret_key;
        $add->authorize_app_key = $request->authorize_app_key;
        $add->authorize_secret_key = $request->authorize_secret_key;
        $add->back_text = $request->back_text;
        $add->back_link = $request->back_link;
        $add->footer_text = $request->footer_text;
        $add->guest_list_button_text = $request->guest_list_button_text ?: 'Guest List';
        $add->guest_tab_subtitle = $request->guest_tab_subtitle ?: null;
        $add->guest_tab_color = $request->guest_tab_color ?: null;
        $add->guest_tab_icon = $request->guest_tab_icon ?: null;
        $add->package_button_text = $request->package_button_text ?: 'Packages';
        $add->package_tab_subtitle = $request->package_tab_subtitle ?: null;
        $add->package_tab_color = $request->package_tab_color ?: null;
        $add->package_tab_icon = $request->package_tab_icon ?: null;
        $add->package_tab_ribbon = $request->package_tab_ribbon ?: null;
        $add->transportation_confirmation_text = $request->transportation_confirmation_text;
        $add->operating_days = $this->normalizeOperatingDays($request->input('operating_days', []));
        $add->operating_start_time = $request->filled('operating_start_time') ? $request->operating_start_time : null;
        $add->operating_end_time = $request->filled('operating_end_time') ? $request->operating_end_time : null;
        $add->pickup_start_time = $request->filled('pickup_start_time') ? $request->pickup_start_time : null;
        $add->pickup_end_time = $request->filled('pickup_end_time') ? $request->pickup_end_time : null;

        $image = $request->file('logo');
        if ($image) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
            $add->logo = $imageName;

        }

        // Handle logo dimensions
        $add->logo_width = $request->logo_width;
        $add->logo_height = $request->logo_height;

        $galleryImages = [];
        foreach ($this->normalizeImageFiles($request->file('gallery_images')) as $index => $image) {
            $name = 'website_gallery_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $name);
            $galleryImages[] = $name;
        }
        if (!empty($galleryImages)) {
            $add->gallery_images = $galleryImages;
        }

        $add->save();

        $assignablePermissionIds = Permission::query()
            ->where('is_super_admin_only', false)
            ->pluck('id')
            ->all();

        $websiteAdminRole = WebsiteRole::firstOrCreate(
            [
                'website_id' => $add->id,
                'slug' => 'website-admin',
            ],
            [
                'name' => 'Website Admin',
                'description' => 'Full website access except super-admin-only platform settings.',
                'is_website_admin' => true,
                'is_system' => true,
            ]
        );
        $websiteAdminRole->permissions()->sync($assignablePermissionIds);

        $transactionRole = WebsiteRole::firstOrCreate(
            [
                'website_id' => $add->id,
                'slug' => 'transaction-staff',
            ],
            [
                'name' => 'Transaction Staff',
                'description' => 'Access to transactions and scanner only.',
                'is_website_admin' => false,
                'is_system' => true,
            ]
        );

        $transactionPermissionIds = Permission::query()
            ->whereIn('key', [
                'admin.transaction.index',
                'admin.transaction.affiliate',
                'admin.transaction.entertainer',
                'admin.transaction.show',
                'admin.transaction.update',
                'admin.transaction.scan',
                'admin.transaction.scan.lookup',
                'admin.transaction.scan.check-in',
            ])
            ->pluck('id')
            ->all();
        $transactionRole->permissions()->sync($transactionPermissionIds);

        User::create([
            'name' => $request->website_admin_name,
            'email' => $request->website_admin_email,
            'password' => $adminPasswordHash,
            'website_id' => $add->id,
            'website_role_id' => $websiteAdminRole->id,
            'user_type' => 'website_user',
        ]);

        foreach ($emails as $key => $value) {
            $email = new Email;
            $email->name = $value['name'] ?? null;
            $email->website_id = $add->id;
            $email->email = $value['email'] ?? null;
            $email->save();
        }

        $smtp = new SMTP;
        $smtp->host = $request->host;
        $smtp->username = $request->username;
        $smtp->password = $request->password;
        $smtp->port = $request->port;
        $smtp->encryption = $request->encryption;
        $smtp->website_id = $add->id;
        $smtp->from_name = $request->from_name;
        $smtp->from_email = $request->from_address;
        $smtp->save();

        $this->syncWebsitePaymentLogos($add, $request->input('payment_methods', []));

        return redirect()->route('admin.website.index')->with('success', 'Website created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = Website::with('paymentLogos')->findOrFail($id);
        $stockPaymentLogos = $this->getStockPaymentLogos();
        $selectedPaymentMethodKeys = $data->paymentLogos
            ->pluck('logo')
            ->map(fn ($logo) => strtolower(trim((string) $logo)))
            ->filter(fn ($logo) => array_key_exists($logo, $stockPaymentLogos))
            ->values()
            ->all();

        if (empty($selectedPaymentMethodKeys)) {
            $selectedPaymentMethodKeys = array_keys($stockPaymentLogos);
        }

        $websiteAdminUser = User::where('website_id', $data->id)
            ->where('user_type', 'website_user')
            ->whereHas('websiteRole', function ($query) {
                $query->where('is_website_admin', true);
            })
            ->orderBy('id')
            ->first();
        
        // Check authorization for website users
        $this->authorizeWebsiteAccess($data->id, 'Access denied. You can only edit your own website.');
        
        return view('admin.website.edit', compact('data', 'websiteAdminUser', 'stockPaymentLogos', 'selectedPaymentMethodKeys'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = Website::find($id);
        $websiteAdminUser = User::where('website_id', $add->id)
            ->where('user_type', 'website_user')
            ->whereHas('websiteRole', function ($query) {
                $query->where('is_website_admin', true);
            })
            ->orderBy('id')
            ->first();

        $request->validate([
            'website_admin_name' => 'required|string|max:255',
            'website_admin_email' => 'required|email|max:255',
            'website_admin_password' => 'nullable|string|min:8|confirmed',
            'google_analytics_id' => 'nullable|string|max:64|regex:/^[A-Za-z0-9_-]+$/',
            'hero_badge_1_label' => 'nullable|string|max:80',
            'hero_badge_1_sub' => 'nullable|string|max:120',
            'hero_badge_2_label' => 'nullable|string|max:80',
            'hero_badge_2_sub' => 'nullable|string|max:120',
            'package_section_title' => 'nullable|string|max:120',
            'package_section_subtext' => 'nullable|string|max:255',
            'guest_tab_subtitle' => 'nullable|string|max:120',
            'package_tab_subtitle' => 'nullable|string|max:120',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'in:visa,mastercard,amex,google_pay,apple_pay',
            'operating_days' => 'nullable|array',
            'operating_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'operating_start_time' => 'nullable|date_format:H:i',
            'operating_end_time' => 'nullable|date_format:H:i',
            'pickup_start_time' => 'nullable|date_format:H:i',
            'pickup_end_time' => 'nullable|date_format:H:i',
            'entertainer_submission_emails' => 'nullable|string',
            'clublifter_enabled' => 'nullable|boolean',
        ]);
        
        // Check authorization for website users
        $this->authorizeWebsiteAccess($add->id, 'Access denied. You can only update your own website.');
        
        // dd($request->all());
        $add->name = $request->name;
        $add->domain = $request->domain;
        $add->google_analytics_id = $request->filled('google_analytics_id')
            ? strtoupper(trim((string) $request->google_analytics_id))
            : null;
        
        // Update slug if provided, otherwise regenerate from name
        if ($request->slug && $request->slug !== $add->slug) {
            $add->slug = Website::generateSlug($request->slug, $id);
        } elseif (!$add->slug) {
            // Generate slug if doesn't exist
            $add->slug = Website::generateSlug($request->name, $id);
        }
        
        if ($request->has('payment_method')) {
            $add->payment_method = $request->payment_method;
        }
        $add->lat = $request->lat;
        $add->long = $request->long;
        $add->reservation = $request->reservation;
        $add->location = $request->location;
        $add->phone = $request->phone;
        $add->email = $request->email;
        $add->policy = $request->policy;
        $add->success_page = self::DEFAULT_SUCCESS_PAGE;
        $add->terms = $request->terms;
        $add->clublifter_enabled = $request->boolean('clublifter_enabled');
        if ($request->has('gratuity_fee')) {
            $add->gratuity_fee = $request->gratuity_fee;
        }
        if ($request->has('gratuity_name')) {
            $add->gratuity_name = $request->gratuity_name;
        }
        if ($request->has('refundable_fee')) {
            $add->refundable_fee = $request->refundable_fee;
        }
        if ($request->has('refundable_name')) {
            $add->refundable_name = $request->refundable_name;
        }
        if ($request->has('sales_tax_fee')) {
            $add->sales_tax_fee = $request->sales_tax_fee;
        }
        if ($request->has('sales_tax_name')) {
            $add->sales_tax_name = $request->sales_tax_name;
        }
        if ($request->has('service_charge_fee')) {
            $add->service_charge_fee = $request->service_charge_fee;
        }
        if ($request->has('service_charge_name')) {
            $add->service_charge_name = $request->service_charge_name;
        }
        if ($request->has('promo_code_name')) {
            $add->promo_code_name = $request->promo_code_name;
        }
        $add->description = $request->description;
        if ($request->has('stripe_app_key')) {
            $add->stripe_app_key = $request->stripe_app_key;
        }
        if ($request->has('stripe_secret_key')) {
            $add->stripe_secret_key = $request->stripe_secret_key;
        }
        if ($request->has('authorize_app_key')) {
            $add->authorize_app_key = $request->authorize_app_key;
        }
        if ($request->has('authorize_secret_key')) {
            $add->authorize_secret_key = $request->authorize_secret_key;
        }
        $add->back_text = $request->back_text;
        $add->back_link = $request->back_link;
        $add->footer_text = $request->footer_text;
        $add->guest_list_button_text = $request->guest_list_button_text ?: 'Guest List';
        $add->guest_tab_subtitle = $request->guest_tab_subtitle ?: null;
        $add->guest_tab_color = $request->guest_tab_color ?: null;
        $add->guest_tab_icon = $request->guest_tab_icon ?: null;
        $add->package_button_text = $request->package_button_text ?: 'Packages';
        $add->package_tab_subtitle = $request->package_tab_subtitle ?: null;
        $add->package_tab_color = $request->package_tab_color ?: null;
        $add->package_tab_icon = $request->package_tab_icon ?: null;
        $add->package_tab_ribbon = $request->package_tab_ribbon ?: null;
        $add->transportation_confirmation_text = $request->transportation_confirmation_text;
        $add->operating_days = $this->normalizeOperatingDays($request->input('operating_days', []));
        $add->operating_start_time = $request->filled('operating_start_time') ? $request->operating_start_time : null;
        $add->operating_end_time = $request->filled('operating_end_time') ? $request->operating_end_time : null;
        $add->pickup_start_time = $request->filled('pickup_start_time') ? $request->pickup_start_time : null;
        $add->pickup_end_time = $request->filled('pickup_end_time') ? $request->pickup_end_time : null;
        $add->text_description = self::DEFAULT_TEXT_DESCRIPTION;
        $add->secondary_description = $request->secondary_description;
        $add->description_label = self::DEFAULT_DESCRIPTION_LABEL;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->hero_badge_1_label = $request->hero_badge_1_label;
        $add->hero_badge_1_sub = $request->hero_badge_1_sub;
        $add->hero_badge_2_label = $request->hero_badge_2_label;
        $add->hero_badge_2_sub = $request->hero_badge_2_sub;
        $add->package_section_title = $request->package_section_title;
        $add->package_section_subtext = $request->package_section_subtext;

        $image = $request->file('logo');
        if ($image) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
            $add->logo = $imageName;

        }

        // Handle logo dimensions
        $add->logo_width = $request->logo_width;
        $add->logo_height = $request->logo_height;

        $currentGalleryImages = array_values(array_filter((array) $add->gallery_images));
        $existingGalleryImages = $this->decodeGalleryImages($request->input('existing_gallery_images'));
        $newGalleryImages = [];

        foreach ($this->normalizeImageFiles($request->file('gallery_images')) as $index => $image) {
            $name = 'website_gallery_' . $add->id . '_' . time() . '_' . $index . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $name);
            $newGalleryImages[] = $name;
        }

        $finalGalleryImages = array_values(array_filter(array_merge($existingGalleryImages, $newGalleryImages)));
        $add->gallery_images = !empty($finalGalleryImages) ? $finalGalleryImages : null;

        $removedGalleryImages = array_diff($currentGalleryImages, $finalGalleryImages);
        foreach ($removedGalleryImages as $removedImage) {
            $path = public_path('uploads/' . $removedImage);
            if ($removedImage && file_exists($path)) {
                @unlink($path);
            }
        }

         $emails = $this->normalizeNotificationEmails($request->input('emails', '[]'));
         $entertainerSubmissionEmails = $this->normalizeNotificationEmails($request->input('entertainer_submission_emails', '[]'));

         $de = Email::where('website_id', $id)->delete();

         foreach ($emails as $key => $value) {
            $email = new Email;
            $email->name = $value['name'] ?? null;
            $email->website_id = $add->id;
            $email->email = $value['email'] ?? null;
            $email->save();
        }

        $add->entertainer_submission_emails = $entertainerSubmissionEmails;

        $adminEmail = $request->website_admin_email;

        // Block emails already used by any other account type.
        if (User::where('email', $adminEmail)
                ->where('user_type', '!=', 'website_user')
                ->when($websiteAdminUser, fn ($q) => $q->where('id', '!=', $websiteAdminUser->id))
                ->exists()) {
            return back()->withInput()->withErrors([
                'website_admin_email' => 'This email is already registered to another account type and cannot be used as a website admin.',
            ]);
        }

        // A website admin with this email on another website shares the same password.
        $sharedWebsiteAdmin = User::where('email', $adminEmail)
            ->where('user_type', 'website_user')
            ->when($websiteAdminUser, fn ($q) => $q->where('id', '!=', $websiteAdminUser->id))
            ->first();

        if ($websiteAdminUser) {
            $websiteAdminUser->name = $request->website_admin_name;
            $websiteAdminUser->email = $adminEmail;

            if ($request->filled('website_admin_password')) {
                // A newly entered password wins and is mirrored to every row sharing this email.
                $hash = Hash::make($request->website_admin_password);
                $websiteAdminUser->password = $hash;
                $websiteAdminUser->save();
                User::where('email', $adminEmail)
                    ->where('id', '!=', $websiteAdminUser->id)
                    ->update(['password' => $hash]);
            } else {
                if ($sharedWebsiteAdmin) {
                    $websiteAdminUser->password = $sharedWebsiteAdmin->password; // keep the shared password
                }
                $websiteAdminUser->save();
            }
        } else {
            if (!$sharedWebsiteAdmin && !$request->filled('website_admin_password')) {
                return back()->withInput()->withErrors([
                    'website_admin_password' => 'A password is required for a new website admin.',
                ]);
            }

            $websiteAdminRole = WebsiteRole::where('website_id', $add->id)
                ->where('is_website_admin', true)
                ->orderBy('id')
                ->first();

            $hash = $request->filled('website_admin_password')
                ? Hash::make($request->website_admin_password)
                : $sharedWebsiteAdmin->password;

            $newAdmin = User::create([
                'name' => $request->website_admin_name,
                'email' => $adminEmail,
                'password' => $hash,
                'website_id' => $add->id,
                'website_role_id' => optional($websiteAdminRole)->id,
                'user_type' => 'website_user',
            ]);

            if ($request->filled('website_admin_password')) {
                // Keep any other rows for this email in sync with the new password.
                User::where('email', $adminEmail)
                    ->where('id', '!=', $newAdmin->id)
                    ->update(['password' => $hash]);
            }
        }

        $add->update();

        $smtp = SMTP::where('website_id', $id)->first();
        $smtp->host = $request->host;
        $smtp->username = $request->username;
        $smtp->password = $request->password;
        $smtp->port = $request->port;
        $smtp->encryption = $request->encryption;
        $smtp->website_id = $add->id;
        $smtp->from_name = $request->from_name;
        $smtp->from_email = $request->from_address;
        $smtp->save();

        $this->syncWebsitePaymentLogos($add, $request->input('payment_methods', []));

        return redirect()->route('admin.website.index')->with('success', 'Website updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function normalizeImageFiles($files): array
    {
        if (!$files) {
            return [];
        }

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (is_array($files)) {
            return array_values(array_filter($files, fn ($file) => $file instanceof UploadedFile));
        }

        return [];
    }

    private function getStockPaymentLogos(): array
    {
        return self::STOCK_PAYMENT_LOGO_CATALOG;
    }

    private function syncWebsitePaymentLogos(Website $website, array $paymentMethods): void
    {
        $stockPaymentLogos = $this->getStockPaymentLogos();
        $allowedKeys = array_keys($stockPaymentLogos);

        $selectedKeys = collect($paymentMethods)
            ->map(fn ($method) => strtolower(trim((string) $method)))
            ->filter(fn ($method) => in_array($method, $allowedKeys, true))
            ->unique()
            ->values()
            ->all();

        PaymentLogo::where('website_id', $website->id)->delete();

        foreach ($selectedKeys as $order => $key) {
            PaymentLogo::create([
                'website_id' => $website->id,
                'name' => $stockPaymentLogos[$key]['name'],
                'logo' => $key,
                'order' => $order,
                'is_active' => true,
            ]);
        }
    }

    private function decodeGalleryImages($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => is_string($item) && $item !== ''));
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, fn ($item) => is_string($item) && $item !== ''));
    }

    private function normalizeOperatingDays($operatingDays): array
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return collect((array) $operatingDays)
            ->map(fn ($day) => strtolower(trim((string) $day)))
            ->filter(fn ($day) => in_array($day, $validDays, true))
            ->unique()
            ->values()
            ->all();
    }

    private function normalizeNotificationEmails($value): array
    {
        if (is_array($value)) {
            $decoded = $value;
        } elseif (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            if (!is_array($decoded)) {
                return [];
            }
        } else {
            return [];
        }

        return collect($decoded)
            ->map(function ($item) {
                if (is_object($item)) {
                    $item = (array) $item;
                }

                if (!is_array($item)) {
                    return null;
                }

                $name = trim((string) ($item['name'] ?? ''));
                $email = strtolower(trim((string) ($item['email'] ?? '')));

                if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return null;
                }

                return [
                    'name' => $name,
                    'email' => $email,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
