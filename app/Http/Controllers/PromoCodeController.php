<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PromoCode;
use App\Models\Website;

class PromoCodeController extends Controller
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

        return view('admin.promo_code.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = PromoCode::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage promo codes for your own website.');
        }
        
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = PromoCode::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage promo codes for your own website.');
        }
        
        $data->is_archieved = 0;
        $data->update();

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
            abort(403, 'Access denied. You can only create promo codes for your own website.');
        }
        
        return view('admin.promo_code.create', compact('id',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only create promo codes for your own website.');
        }
        
        // dd($request->all());
        $add = new PromoCode;
        $add->name = $request->name;
        $add->percentage = $request->percentage;
        $add->promo_code = $request->promo_code;
        $add->type = $request->type;
        $add->description = $request->description;
        $add->website_id = $request->website_id;
        $add->save();

        return redirect()->route('admin.promo_code.show', $add->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view promo codes for your own website.');
        }
        
        $data = PromoCode::where('website_id', $id)
        ->get();

        $website_id = $id;

        return view('admin.promo_code.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = PromoCode::find($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit promo codes for your own website.');
        }
        
        // dd('s');
        return view('admin.promo_code.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = PromoCode::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $add->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update promo codes for your own website.');
        }
        
        // dd('s');
        $add->name = $request->name;
        $add->percentage = $request->percentage;
        $add->promo_code = $request->promo_code;
        $add->type = $request->type;
        $add->description = $request->description;
        $add->update();


        return redirect()->route('admin.promo_code.show', $add->website_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
