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
        
        $data = Setting::find($id);
        $data->authorize_key = $request->authorize_key;
        $data->authorize_secret = $request->authorize_secret;
        $data->stripe_key = $request->stripe_key;
        $data->stripe_secret = $request->stripe_secret;
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
