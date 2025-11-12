<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Package;
use App\Models\Addon;
use App\Models\GeneralAddon;
use App\Models\Event;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Website::where('is_archieved',0)->get();

        return view('admin.package.index', compact('data'));
    }

    public function archive($id)
    {
        $data = Package::where('id',$id)->first();
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $data = Package::where('id',$id)->first();
        $data->is_archieved = 0;
        $data->update();

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $events = Event::where('website_id', $id)->get();
        $addons = GeneralAddon::where('website_id', $id)->get();
        return view('admin.package.create', compact('id', 'events', 'addons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $add = new Package;
        $add->name = $request->name;
        $add->price = $request->price;
        $add->description = $request->description;
        $add->status = $request->status;
        $add->multiple = isset($request->multiple) ? 1 :0;
        $add->transportation = isset($request->transportation) ? 1 :0;
        $add->number_of_guest = $request->number_of_guest;
        $add->website_id = $request->website_id;
        $add->event_id = $request->event_id;
        $add->save();

        $addons = explode(',', $request->addons);


        // $del = Addon::where('package_id', $data->id)->delete();


        foreach ($addons as $key => $value) {

            $addon = GeneralAddon::where('id', $value)->first();

            $addona = new Addon;
            $addona->name = $addon->name;
            $addona->addon_id = $addon->id;
            $addona->price = $addon->price;
            $addona->description = $addon->description;
            $addona->status = $addon->status;
            $addona->package_id = $add->id;
            $addona->save();

        }

        return redirect()->route('admin.package.show', $add->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Package::where('website_id', $id)->get();

        $website_id = $id;

        return view('admin.package.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Package::findOrFail($id);

        $events = Event::where('website_id', $data->website_id)->get();

        $addons = GeneralAddon::where('website_id', $data->website_id)->get();

        return view('admin.package.edit', compact('data', 'id', 'events', 'addons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $data = Package::findOrFail($id);
        $data->name = $request->name;
        $data->price = $request->price;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->multiple = isset($request->multiple) ? 1 :0;
        $data->transportation = isset($request->transportation) ? 1 :0;
        $data->number_of_guest = $request->number_of_guest;
        // $data->website_id = $request->website_id;
        $data->event_id = $request->event_id;
        $data->update();

        $addons = explode(',', $request->addons);


        $del = Addon::where('package_id', $data->id)->delete();
        


        foreach ($addons as $key => $value) {

            $addon = GeneralAddon::where('id', $value)->first();

            if ($addon) {
                # code...
                $addona = new Addon;
                $addona->name = $addon->name;
                $addona->addon_id = $addon->id;
                $addona->price = $addon->price;
                $addona->description = $addon->description;
                $addona->status = $addon->status;
                $addona->package_id = $data->id;
                $addona->save();
            }


        }

        return redirect()->route('admin.package.show', $data->website_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
