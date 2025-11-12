<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Event;
use App\Models\Package;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Website::where('is_archieved',0)->get();

        return view('admin.event.index', compact('data'));
    }

    public function archive($id)
    {
        $data = Event::where('id',$id)->first();
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $data = Event::where('id',$id)->first();
        $data->is_archieved = 0;
        $data->update();

        return back();
    } 

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.event.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $time = $request->time_start.' - '.$request->time_end;

        $add = new Event;
        $add->name = $request->name;
        $add->date = $request->date;
        $add->description = $request->description;
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $add->image = $filename;
        }
        
        // Set logo dimensions if provided
        $add->logo_width = $request->logo_width;
        $add->logo_height = $request->logo_height;

        $add->website_id = $request->website_id;
        $add->time = $time;
        $add->is_booking_paid = $request->is_booking_paid;
        $add->booking_fee = $request->booking_fee;
        $add->save();

        return redirect()->route('admin.event.show', $add->website_id);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Event::where('website_id', $id)->get();

        $website_id = $id;

        return view('admin.event.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Event::find($id);

        return view('admin.event.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $time = $request->time_start.' - '.$request->time_end;

        $add = Event::findOrFail($id);
        $add->name = $request->name;
        $add->date = $request->date;
        $add->description = $request->description;
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $add->image = $filename;
        }
        
        // Set logo dimensions if provided
        $add->logo_width = $request->logo_width;
        $add->logo_height = $request->logo_height;

        $add->time = $time;
        $add->is_booking_paid = $request->is_booking_paid;
        $add->booking_fee = $request->booking_fee;
        $add->update();


        return redirect()->route('admin.event.show', $add->website_id);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
