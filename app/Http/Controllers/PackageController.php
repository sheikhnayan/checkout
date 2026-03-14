<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use App\Models\Package;
use App\Models\Addon;
use App\Models\GeneralAddon;
use App\Models\Event;
use App\Models\PackageCategory;

class PackageController extends Controller
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

        return view('admin.package.index', compact('data'));
    }

    public function archive($id)
    {
        $user = auth()->user();
        $data = Package::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage packages for your own website.');
        }
        
        $data->is_archieved = 1;
        $data->status = 0;
        $data->save();

        return back();
    } 

    public function unarchive($id)
    {
        $user = auth()->user();
        $data = Package::where('id',$id)->first();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage packages for your own website.');
        }
        
        $data->is_archieved = 0;
        $data->status = 1;
        $data->save();

        return back();
    } 

    public function toggleStatus($id)
    {
        $user = auth()->user();
        $package = Package::findOrFail($id);

        if ($user->isWebsiteUser() && $package->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only manage packages for your own website.');
        }

        $package->status = $package->status == 1 ? 0 : 1;
        $package->save();

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
            abort(403, 'Access denied. You can only create packages for your own website.');
        }
        
        $events = Event::where('website_id', $id)->where('is_archieved', 0)->get();
        $addons = GeneralAddon::where('website_id', $id)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->get();
        $categories = PackageCategory::where('website_id', $id)->orderBy('name')->get();

        return view('admin.package.create', compact('id', 'events', 'addons', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $request->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only create packages for your own website.');
        }
        
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
        $add->package_category_id = $this->resolveCategoryId($request, $request->website_id);
        $add->event_id = $request->event_id;
        $add->save();

        $addons = array_filter(explode(',', (string) $request->addons));

        foreach ($addons as $value) {
            $addon = GeneralAddon::where('id', $value)->first();

            if (!$addon) {
                continue;
            }

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
        $user = auth()->user();
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $id != $user->website_id) {
            abort(403, 'Access denied. You can only view packages for your own website.');
        }
        
        $data = Package::with('category')->where('website_id', $id)->get();

        $website_id = $id;

        $categories = PackageCategory::where('website_id', $id)->orderBy('name')->get();

        return view('admin.package.show', compact('data', 'website_id', 'categories'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only edit packages for your own website.');
        }

        $events = Event::where('website_id', $data->website_id)->where('is_archieved', 0)->get();

        $addons = GeneralAddon::where('website_id', $data->website_id)
            ->where('is_archieved', 0)
            ->where('status', 1)
            ->get();

        $categories = PackageCategory::where('website_id', $data->website_id)->orderBy('name')->get();

        return view('admin.package.edit', compact('data', 'id', 'events', 'addons', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        $data = Package::findOrFail($id);
        
        // Check authorization for website users
        if ($user->isWebsiteUser() && $data->website_id != $user->website_id) {
            abort(403, 'Access denied. You can only update packages for your own website.');
        }
        
        // dd($request->all());
        $data->name = $request->name;
        $data->price = $request->price;
        $data->description = $request->description;
        $data->status = $request->status;
        $data->multiple = isset($request->multiple) ? 1 :0;
        $data->transportation = isset($request->transportation) ? 1 :0;
        $data->number_of_guest = $request->number_of_guest;
        $data->package_category_id = $this->resolveCategoryId($request, $data->website_id);
        // $data->website_id = $request->website_id;
        $data->event_id = $request->event_id;
        $data->update();

        $addons = array_filter(explode(',', (string) $request->addons));


        $del = Addon::where('package_id', $data->id)->delete();
        


        foreach ($addons as $value) {

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

    private function resolveCategoryId(Request $request, $websiteId)
    {
        $newCategoryName = trim((string) $request->input('new_category_name'));

        if ($newCategoryName !== '') {
            return PackageCategory::firstOrCreate([
                'website_id' => (int) $websiteId,
                'name' => $newCategoryName,
            ])->id;
        }

        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return null;
        }

        $category = PackageCategory::where('id', $categoryId)
            ->where('website_id', (int) $websiteId)
            ->first();

        return $category ? $category->id : null;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
