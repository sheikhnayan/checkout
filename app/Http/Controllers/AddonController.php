<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralAddon;
use App\Models\Website;
use App\Models\Addon;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $data = Website::where('is_archieved',0)->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website users can only see their own website
            $data = Website::where('id', $user->website_id)->where('is_archieved',0)->get();
        } else {
            $data = collect();
        }

        return view('admin.addon.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = GeneralAddon::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage addons for your own website.');
        }
        
        $data->is_archieved = 1;
        $data->status = 0;
        $data->save();

            // Deactivate all package-level addon instances linked to this general addon
            Addon::where('addon_id', $id)->update(['status' => 0]);

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = GeneralAddon::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage addons for your own website.');
        }
        
        $data->is_archieved = 0;
        $data->status = 1;
        $data->save();

            // Re-activate all package-level addon instances linked to this general addon
            Addon::where('addon_id', $id)->update(['status' => 1]);

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only create addons for your own website.');
        }
        
        return view('admin.addon.create', compact('id',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only create addons for your own website.');
        }
        
        // dd($request->all());
        $add = new GeneralAddon;
        $add->name = $request->name;
        $add->price = $request->price;
        $add->description = $request->description;
        $add->status = $request->status ?? 1;
        $add->website_id = $request->website_id;
        $add->save();

        return redirect()->route('admin.addon.show', $add->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view addons for your own website.');
        }
        
        $data = GeneralAddon::where('website_id', $id)->get();

        $website_id = $id;

        return view('admin.addon.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = GeneralAddon::find($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit addons for your own website.');
        }

        return view('admin.addon.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = GeneralAddon::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $add->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update addons for your own website.');
        }

        // dd($request->all());

        $add = GeneralAddon::findOrFail($id);
        $add->name = $request->name;
        $add->price = $request->price;
        $add->description = $request->description;
        $add->status = $request->status ?? $add->status ?? 1;
        $add->update();


        return redirect()->route('admin.addon.show', $add->website_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
           $user = auth()->user();
           $data = GeneralAddon::findOrFail($id);

           if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
              abort(403, 'Access denied. You can only delete addons for your own website.');
           }

           $website_id = $data->website_id;

           // Delete all package-level addon instances linked to this general addon
           Addon::where('addon_id', $id)->delete();

           $data->delete();

           return redirect()->route('admin.addon.show', $website_id);
    }

    public function toggleStatus(string $id)
    {
        $user = auth()->user();
        $data = GeneralAddon::findOrFail($id);

        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage addons for your own website.');
        }

        $data->status = (string) ((int) $data->status === 1 ? 0 : 1);
        $data->save();

        Addon::where('addon_id', $id)->update(['status' => (int) $data->status]);

        return back();
    }
}
