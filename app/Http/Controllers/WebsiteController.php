<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Email;
use App\Models\SMTP;
use App\Models\PaymentLogo;

class WebsiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $data = Website::all();

        return view('admin.website.index', compact('data'));
    }

    public function archive($id)
    {
        $data = Website::where('id',$id)->first();
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $data = Website::where('id',$id)->first();
        $data->is_archieved = 0;
        $data->update();

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.website.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $emails = json_decode($request->emails);

        $add = new Website;
        $add->name = $request->name;
        $add->domain = $request->domain;
        
        // Generate slug from name or use provided slug
        if ($request->slug) {
            $add->slug = Website::generateSlug($request->slug);
        } else {
            $add->slug = Website::generateSlug($request->name);
        }
        
        $add->payment_method = $request->payment_method;
        $add->lat = $request->lat;
        $add->long = $request->long;
        $add->location = $request->location;
        $add->policy = $request->policy;
        $add->terms = $request->terms;
        $add->success_page = $request->success_page;
        $add->font_color = $request->font_color;
        $add->text_description = $request->text_description;
        $add->description_label = $request->description_label;
        $add->phone = $request->phone;
        $add->color = $request->color;
        $add->secondary_color = $request->secondary_color;
        $add->background_color = $request->background_color;
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
        $add->guest_list_button_text = $request->guest_list_button_text;
        $add->package_button_text = $request->package_button_text;
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

        $add->save();

        foreach ($emails as $key => $value) {
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
            foreach ($request->payment_logos as $logoData) {
                if (!empty($logoData['name'])) {
                    $paymentLogo = new PaymentLogo();
                    $paymentLogo->website_id = $add->id;
                    $paymentLogo->name = $logoData['name'];
                    $paymentLogo->order = $logoData['order'] ?? 0;
                    $paymentLogo->is_active = $logoData['is_active'] ?? 1;

                    // Handle logo file upload
                    if (isset($logoData['logo']) && $logoData['logo']) {
                        $logoFile = $logoData['logo'];
                        $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                        $logoFile->move(public_path('uploads'), $logoName);
                        $paymentLogo->logo = $logoName;
                    }

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
        $data = Website::with('paymentLogos')->findOrFail($id);
        return view('admin.website.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $add = Website::find($id);
        $add->name = $request->name;
        $add->domain = $request->domain;
        
        // Update slug if provided, otherwise regenerate from name
        if ($request->slug && $request->slug !== $add->slug) {
            $add->slug = Website::generateSlug($request->slug, $id);
        } elseif (!$add->slug) {
            // Generate slug if doesn't exist
            $add->slug = Website::generateSlug($request->name, $id);
        }
        
        $add->payment_method = $request->payment_method;
        $add->lat = $request->lat;
        $add->long = $request->long;
        $add->reservation = $request->reservation;
        $add->location = $request->location;
        $add->phone = $request->phone;
        $add->color = $request->color;
        $add->secondary_color = $request->secondary_color;
        $add->background_color = $request->background_color;
        $add->email = $request->email;
        $add->policy = $request->policy;
        $add->success_page = $request->success_page;
        $add->terms = $request->terms;
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
        $add->guest_list_button_text = $request->guest_list_button_text;
        $add->package_button_text = $request->package_button_text;
        $add->transportation_confirmation_text = $request->transportation_confirmation_text;
        $add->font_color = $request->font_color;
        $add->text_description = $request->text_description;
        $add->description_label = $request->description_label;

        $image = $request->file('logo');
        if ($image) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads'), $imageName);
            $add->logo = $imageName;

        }

        // Handle logo dimensions
        $add->logo_width = $request->logo_width;
        $add->logo_height = $request->logo_height;

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
            dd('r');
            foreach ($request->payment_logos as $logoData) {
                if (!empty($logoData['name'])) {
                    // Check if this is an existing logo (has ID) or new one
                    if (isset($logoData['id']) && $logoData['id']) {
                        // Update existing logo
                        $paymentLogo = PaymentLogo::find($logoData['id']);
                        if ($paymentLogo) {
                            $paymentLogo->name = $logoData['name'];
                            $paymentLogo->order = $logoData['order'] ?? 0;
                            $paymentLogo->is_active = $logoData['is_active'] ?? 1;

                            // Handle logo file upload if new file provided
                            if (isset($logoData['logo']) && $logoData['logo']) {
                                $logoFile = $logoData['logo'];
                                $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                                $logoFile->move(public_path('uploads'), $logoName);
                                
                                // Delete old logo file if exists
                                if ($paymentLogo->logo && file_exists(public_path('uploads/' . $paymentLogo->logo))) {
                                    unlink(public_path('uploads/' . $paymentLogo->logo));
                                }
                                
                                $paymentLogo->logo = $logoName;
                            }

                            $paymentLogo->save();
                        }
                    } else {
                        // Create new logo
                        $paymentLogo = new PaymentLogo();
                        $paymentLogo->website_id = $add->id;
                        $paymentLogo->name = $logoData['name'];
                        $paymentLogo->order = $logoData['order'] ?? 0;
                        $paymentLogo->is_active = $logoData['is_active'] ?? 1;

                        // Handle logo file upload
                        if (isset($logoData['logo']) && $logoData['logo']) {
                            $logoFile = $logoData['logo'];
                            $logoName = time() . '_' . uniqid() . '.' . $logoFile->getClientOriginalExtension();
                            $logoFile->move(public_path('uploads'), $logoName);
                            $paymentLogo->logo = $logoName;
                        }

                        $paymentLogo->save();
                    }
                }
            }

            // Remove logos that are not in the submitted data (deleted by user)
            $submittedIds = array_filter(array_column($request->payment_logos, 'id'));
            PaymentLogo::where('website_id', $id)
                ->whereNotIn('id', $submittedIds)
                ->delete();
        }
        dd($request->all());


        return redirect()->route('admin.website.index')->with('success', 'Website updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
