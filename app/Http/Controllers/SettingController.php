<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Setting;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        
        // Only admins can access global settings
        if ($user->isWebsiteUser()) {
            abort(403, 'Access denied. Global payment settings are only accessible to administrators. Please configure payment settings for your website in the Website settings.');
        }
        
        $data = Setting::find($id);

        return view('admin.setting.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        
        // Only admins can update global settings
        if ($user->isWebsiteUser()) {
            abort(403, 'Access denied. Global payment settings are only accessible to administrators.');
        }

        $validated = $request->validate([
            'google_analytics_measurement_id' => 'nullable|string|max:64|regex:/^[A-Za-z0-9_-]+$/',
        ]);
        
        $data = Setting::find($id);
        $data->authorize_key = $request->authorize_key;
        $data->authorize_secret = $request->authorize_secret;
        $data->stripe_key = $request->stripe_key;
        $data->stripe_secret = $request->stripe_secret;
        $data->google_analytics_measurement_id = !empty($validated['google_analytics_measurement_id'])
            ? strtoupper(trim((string) $validated['google_analytics_measurement_id']))
            : null;
        // Global Live/Sandbox toggle. An unchecked checkbox is not submitted, so
        // read it explicitly: checked => sandbox (true), unchecked => live (false).
        $data->sandbox_mode = $request->boolean('sandbox_mode');
        $data->update();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
