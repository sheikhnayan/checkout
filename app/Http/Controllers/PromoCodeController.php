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
        $data = Website::where('is_archieved',0)->get();

        return view('admin.promo_code.index', compact('data'));
    }

    public function archive($id)
    {
        $data = PromoCode::where('id',$id)->first();
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $data = PromoCode::where('id',$id)->first();
        $data->is_archieved = 0;
        $data->update();

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.promo_code.create', compact('id',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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
        // dd('s');
        $data = PromoCode::find($id);

        return view('admin.promo_code.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd('s');
        $add = PromoCode::findOrFail($id);
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
