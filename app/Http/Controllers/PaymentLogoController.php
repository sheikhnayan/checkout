<?php

namespace App\Http\Controllers;

use App\Models\PaymentLogo;
use Illuminate\Http\Request;

class PaymentLogoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'website_id' => 'required|exists:websites,id',
            'payment_name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'order' => 'nullable|integer|min:0'
        ]);

        $logoName = null;
        if ($request->hasFile('logo')) {
            $logoName = time() . '.' . $request->file('logo')->getClientOriginalExtension();
            $request->file('logo')->move(public_path('uploads'), $logoName);
        }

        PaymentLogo::create([
            'website_id' => $request->website_id,
            'name' => $request->payment_name,
            'logo' => $logoName,
            'order' => $request->order ?? 0,
            'is_active' => true
        ]);

        return back()->with('success', 'Payment logo added successfully!');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $paymentLogo = PaymentLogo::findOrFail($id);
        
        // Delete the logo file
        if (file_exists(public_path('uploads/' . $paymentLogo->logo))) {
            unlink(public_path('uploads/' . $paymentLogo->logo));
        }
        
        $paymentLogo->delete();
        
        return back()->with('success', 'Payment logo deleted successfully!');
    }
}
