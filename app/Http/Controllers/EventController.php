<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Event;
use App\Models\Package;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

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
        $websiteId = (int) $id;
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $websiteId != (int) $user->website_id) {
            abort(403, 'Access denied. You can only create events for your own website.');
        }

        $packages = Package::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        
        return view('admin.event.create', compact('id', 'packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $websiteId = $this->resolveWebsiteId($request, $user);

        if (!$websiteId || !Website::where('id', $websiteId)->exists()) {
            return back()
                ->withErrors(['website_id' => 'The selected website id is invalid.'])
                ->withInput();
        }
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $websiteId != (int) $user->website_id) {
            abort(403, 'Access denied. You can only create events for your own website.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'required|string',
            'secondary_description' => 'nullable|string',
            'image' => 'required|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|image|max:4096',
            'logo_width' => 'nullable|integer|min:1',
            'logo_height' => 'nullable|integer|min:1',
            'attendee_limit' => 'nullable|integer|min:1',
            'time_start' => 'nullable|string|max:30',
            'time_end' => 'nullable|string|max:30',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'nullable|integer',
        ]);

        $startDateInput = $validated['start_date'] ?? $validated['date'] ?? null;
        $startDate = $startDateInput ? Carbon::parse($startDateInput)->format('Y-m-d') : null;
        $endDate = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->format('Y-m-d')
            : $startDate;

        $time = $this->buildTimeRange($request->input('time_start'), $request->input('time_end'));

        $add = new Event;
        $add->name = $request->name;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->date = $startDate;
        $add->start_date = $startDate;
        $add->end_date = $endDate;
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
        $add->attendee_limit = $request->filled('attendee_limit') ? (int) $request->attendee_limit : null;

        $add->website_id = $websiteId;
        $add->time = $time;
        $add->is_booking_paid = 0;
        $add->booking_fee = 0;
        $add->save();

        $selectedPackageIds = $this->sanitizePackageIdsForWebsite($request->input('package_ids', []), $websiteId);
        $this->syncEventPackages($add->id, $websiteId, $selectedPackageIds);

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

        $packages = Package::where('website_id', $data->website_id)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->orderBy('name')
            ->get();

        $selectedPackageIds = Package::where('website_id', $data->website_id)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->where('event_id', $data->id)
            ->pluck('id')
            ->all();

        return view('admin.event.edit', compact('data', 'id', 'packages', 'selectedPackageIds'));
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

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'required|string',
            'secondary_description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'nullable|image|max:4096',
            'logo_width' => 'nullable|integer|min:1',
            'logo_height' => 'nullable|integer|min:1',
            'attendee_limit' => 'nullable|integer|min:1',
            'time_start' => 'nullable|string|max:30',
            'time_end' => 'nullable|string|max:30',
            'package_ids' => 'nullable|array',
            'package_ids.*' => 'nullable|integer',
        ]);

        $startDateInput = $validated['start_date'] ?? $validated['date'] ?? $add->date;
        $startDate = $startDateInput ? Carbon::parse($startDateInput)->format('Y-m-d') : null;
        $endDate = !empty($validated['end_date'])
            ? Carbon::parse($validated['end_date'])->format('Y-m-d')
            : $startDate;

        $time = $this->buildTimeRange($request->input('time_start'), $request->input('time_end'));

        $add = Event::findOrFail($id);
        $add->name = $request->name;
        $add->hero_title = $request->hero_title;
        $add->hero_subtitle = $request->hero_subtitle;
        $add->date = $startDate;
        $add->start_date = $startDate;
        $add->end_date = $endDate;
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
        $add->attendee_limit = $request->filled('attendee_limit') ? (int) $request->attendee_limit : null;

        $add->time = $time;
        $add->is_booking_paid = 0;
        $add->booking_fee = 0;
        $add->update();

        $selectedPackageIds = $this->sanitizePackageIdsForWebsite($request->input('package_ids', []), (int) $add->website_id);
        $this->syncEventPackages($add->id, (int) $add->website_id, $selectedPackageIds);


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

    private function resolveWebsiteId(Request $request, $user): ?int
    {
        if ($user->isWebsiteUser() && $user->website_id) {
            return (int) $user->website_id;
        }

        $rawWebsiteId = $request->input('website_id');

        if ($rawWebsiteId === null || $rawWebsiteId === '') {
            return null;
        }

        $websiteId = (int) $rawWebsiteId;

        return $websiteId > 0 ? $websiteId : null;
    }

    private function buildTimeRange(?string $timeStart, ?string $timeEnd): ?string
    {
        $start = trim((string) $timeStart);
        $end = trim((string) $timeEnd);

        if ($start === '' && $end === '') {
            return null;
        }

        if ($start !== '' && $end !== '') {
            return $start . ' - ' . $end;
        }

        return $start !== '' ? $start : $end;
    }

    private function sanitizePackageIdsForWebsite($packageIds, int $websiteId): array
    {
        $ids = collect($packageIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();

        if (empty($ids)) {
            return [];
        }

        $allowedIds = Package::where('website_id', $websiteId)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->whereIn('id', array_values(array_unique($ids)))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->flip()
            ->all();

        return collect($ids)
            ->filter(fn ($id) => isset($allowedIds[$id]))
            ->values()
            ->all();
    }

    private function syncEventPackages(int $eventId, int $websiteId, array $selectedPackageIds): void
    {
        Package::where('website_id', $websiteId)
            ->where('event_id', $eventId)
            ->whereNotIn('id', $selectedPackageIds)
            ->update(['event_id' => null]);

        if (!empty($selectedPackageIds)) {
            Package::where('website_id', $websiteId)
                ->whereIn('id', $selectedPackageIds)
                ->update(['event_id' => $eventId]);
        }
    }
}
