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

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $data = Website::all();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website users can only see their own website
            $data = Website::where('id', $user->website_id)->get();
        } else {
            $data = collect();
        }

        return view('admin.website.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = Website::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->id != $user->website_id) {
            abort(403, 'Access denied. You can only manage your own website.');
        }
        
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
        if ($user->isWebsiteUser() && $data->id != $user->website_id) {
            abort(403, 'Access denied. You can only manage your own website.');
        }
        
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
        if ($user->isWebsiteUser() && $data->id != $user->website_id) {
            abort(403, 'Access denied. You can only manage your own website.');
        }

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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'website_admin_name' => 'required|string|max:255',
            'website_admin_email' => 'required|email|max:255|unique:users,email',
            'website_admin_password' => 'required|string|min:8|confirmed',
        ]);

        Permission::syncFromAdminRoutes();

        $emails = json_decode($request->emails);

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
        $add->success_page = $request->success_page;
        $add->text_description = $request->text_description;
        $add->secondary_description = $request->secondary_description;
        $add->description_label = $request->description_label;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->phone = $request->phone;
        $add->reservation = $request->reservation;
        $add->email = $request->email;
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
        $add->package_button_text = $request->package_button_text ?: 'Packages';
        $add->transportation_confirmation_text = $request->transportation_confirmation_text;

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
            'password' => Hash::make($request->website_admin_password),
            'website_id' => $add->id,
            'website_role_id' => $websiteAdminRole->id,
            'user_type' => 'website_user',
        ]);

        foreach (($emails ?: []) as $key => $value) {
            # code...
            $email = new Email;
            $email->name = $value->name;
            $email->website_id = $add->id;
            $email->email = $value->email;
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

        // Handle payment logos
        if ($request->has('payment_logos')) {
            // Files must be read from $request->file(), not from $request->input()
            $uploadedLogoFiles = $request->file('payment_logos') ?? [];
            foreach ($request->payment_logos as $key => $logoData) {
                if (!empty($logoData['name'])) {
                    $logoFile = $uploadedLogoFiles[$key]['logo'] ?? null;
                    // Require an image file for new logos (logo column is NOT NULL)
                    if (!$logoFile || !$logoFile->isValid()) {
                        continue;
                    }
                    $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                    $logoFile->move(public_path('uploads'), $logoName);

                    $paymentLogo = new PaymentLogo();
                    $paymentLogo->website_id = $add->id;
                    $paymentLogo->name = $logoData['name'];
                    $paymentLogo->logo = $logoName;
                    $paymentLogo->order = $logoData['order'] ?? 0;
                    $paymentLogo->is_active = $logoData['is_active'] ?? 1;
                    $paymentLogo->save();
                }
            }
        }

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
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->id != $user->website_id) {
            abort(403, 'Access denied. You can only edit your own website.');
        }
        
        return view('admin.website.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = Website::find($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $add->id != $user->website_id) {
            abort(403, 'Access denied. You can only update your own website.');
        }
        
        // dd($request->all());
        $add->name = $request->name;
        $add->domain = $request->domain;
        
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
        $add->success_page = $request->success_page;
        $add->terms = $request->terms;
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
        $add->package_button_text = $request->package_button_text ?: 'Packages';
        $add->transportation_confirmation_text = $request->transportation_confirmation_text;
        $add->text_description = $request->text_description;
        $add->secondary_description = $request->secondary_description;
        $add->description_label = $request->description_label;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;

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

         $emails = json_decode($request->emails);

         $de = Email::where('website_id', $id)->delete();

         foreach ($emails as $key => $value) {
            # code...
            $email = new Email;
            $email->name = $value->name;
            $email->website_id = $add->id;
            $email->email = $value->email;
            $email->save();
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

        // Handle payment logos
        if ($request->payment_logos) {
            // Files must be read from $request->file(), not from $request->input()
            $uploadedLogoFiles = $request->file('payment_logos') ?? [];
            $savedLogoIds = [];

            foreach ($request->payment_logos as $key => $logoData) {
                if (!empty($logoData['name'])) {
                    $logoFile = $uploadedLogoFiles[$key]['logo'] ?? null;

                    if (isset($logoData['id']) && $logoData['id']) {
                        // Update existing logo
                        $paymentLogo = PaymentLogo::find($logoData['id']);
                        if ($paymentLogo) {
                            $paymentLogo->name = $logoData['name'];
                            $paymentLogo->order = $logoData['order'] ?? 0;
                            $paymentLogo->is_active = $logoData['is_active'] ?? 1;

                            if ($logoFile && $logoFile->isValid()) {
                                $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                                $logoFile->move(public_path('uploads'), $logoName);
                                if ($paymentLogo->logo && file_exists(public_path('uploads/' . $paymentLogo->logo))) {
                                    @unlink(public_path('uploads/' . $paymentLogo->logo));
                                }
                                $paymentLogo->logo = $logoName;
                            }

                            $paymentLogo->save();
                            $savedLogoIds[] = $paymentLogo->id;
                        }
                    } else {
                        // Create new logo — require an image file (logo column is NOT NULL)
                        if (!$logoFile || !$logoFile->isValid()) {
                            continue;
                        }
                        $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                        $logoFile->move(public_path('uploads'), $logoName);

                        $paymentLogo = new PaymentLogo();
                        $paymentLogo->website_id = $add->id;
                        $paymentLogo->name = $logoData['name'];
                        $paymentLogo->logo = $logoName;
                        $paymentLogo->order = $logoData['order'] ?? 0;
                        $paymentLogo->is_active = $logoData['is_active'] ?? 1;
                        $paymentLogo->save();
                        $savedLogoIds[] = $paymentLogo->id;
                    }
                }
            }

            // Delete logos that were removed — use $savedLogoIds (includes newly created IDs)
            // to avoid whereNotIn([]) which would delete everything
            if (!empty($savedLogoIds)) {
                PaymentLogo::where('website_id', $id)
                    ->whereNotIn('id', $savedLogoIds)
                    ->delete();
            } else {
                PaymentLogo::where('website_id', $id)->delete();
            }
        }

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
}
