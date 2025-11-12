<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeneralAddon;
use App\Models\Website;

class AddonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Website::where('is_archieved',0)->get();

        return view('admin.addon.index', compact('data'));
    }

    public function archive($id)
    {
        $data = GeneralAddon::where('id',$id)->first();
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $data = GeneralAddon::where('id',$id)->first();
        $data->is_archieved = 0;
        $data->update();

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.addon.create', compact('id',));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $add = new GeneralAddon;
        $add->name = $request->name;
        $add->price = $request->price;
        $add->description = $request->description;
        $add->website_id = $request->website_id;
        $add->save();

        return redirect()->route('admin.addon.show', $add->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = GeneralAddon::where('website_id', $id)->get();

        $website_id = $id;

        return view('admin.addon.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = GeneralAddon::find($id);

        return view('admin.addon.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        // dd($request->all());

        $add = GeneralAddon::findOrFail($id);
        $add->name = $request->name;
        $add->price = $request->price;
        $add->description = $request->description;
        $add->update();


        return redirect()->route('admin.addon.show', $add->website_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
