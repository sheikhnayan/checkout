<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Event;
use App\Models\Package;
use Illuminate\Http\UploadedFile;

class EventController extends Controller
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

        return view('admin.event.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = Event::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage events for your own website.');
        }
        
        $data->is_archieved = 1;
        $data->update();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = Event::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage events for your own website.');
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
            abort(403, 'Access denied. You can only create events for your own website.');
        }
        
        return view('admin.event.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only create events for your own website.');
        }
        
        // dd($request->all());
        $time = $request->time_start.' - '.$request->time_end;

        $add = new Event;
        $add->name = $request->name;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->date = $request->date;
        $add->description = $request->description;
        $add->secondary_description = $request->secondary_description;
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $add->image = $filename;
        }

        $galleryImages = [];
        foreach ($this->normalizeImageFiles($request->file('gallery_images')) as $index => $file) {
            $galleryName = 'event_gallery_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $galleryName);
            $galleryImages[] = $galleryName;
        }

        if (!empty($galleryImages)) {
            $add->gallery_images = $galleryImages;
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
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view events for your own website.');
        }
        
        $data = Event::where('website_id', $id)->get();

        $website_id = $id;

        return view('admin.event.show', compact('data', 'website_id'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = Event::find($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit events for your own website.');
        }

        return view('admin.event.edit', compact('data', 'id'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $add = Event::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $add->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update events for your own website.');
        }

        $time = $request->time_start.' - '.$request->time_end;

        $add = Event::findOrFail($id);
        $add->name = $request->name;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->date = $request->date;
        $add->description = $request->description;
        $add->secondary_description = $request->secondary_description;
        
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $filename);
            $add->image = $filename;
        }

        $currentGalleryImages = array_values(array_filter((array) $add->gallery_images));
        $existingGalleryImages = $this->decodeGalleryImages($request->input('existing_gallery_images'));
        $newGalleryImages = [];

        foreach ($this->normalizeImageFiles($request->file('gallery_images')) as $index => $file) {
            $galleryName = 'event_gallery_' . $add->id . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $galleryName);
            $newGalleryImages[] = $galleryName;
        }

        $finalGalleryImages = array_values(array_filter(array_merge($existingGalleryImages, $newGalleryImages)));
        $add->gallery_images = !empty($finalGalleryImages) ? $finalGalleryImages : null;

        $removedGalleryImages = array_diff($currentGalleryImages, $finalGalleryImages);
        foreach ($removedGalleryImages as $removedImage) {
            $path = public_path('uploads/' . $removedImage);
            if ($removedImage && file_exists($path)) {
                @unlink($path);
            }
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

    private function normalizeImageFiles($files): array
    {
        if (!$files) {
            return [];
        }

        if ($files instanceof UploadedFile) {
            return [$files];
        }

        if (is_array($files)) {
            return array_values(array_filter($files, fn ($file) => $file instanceof UploadedFile));
        }

        return [];
    }

    private function decodeGalleryImages($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($item) => is_string($item) && $item !== ''));
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, fn ($item) => is_string($item) && $item !== ''));
    }
}
