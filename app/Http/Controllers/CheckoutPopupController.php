<?php

namespace App\Http\Controllers;

use App\Models\CheckoutPopup;
use App\Models\Website;
use Illuminate\Http\Request;

class CheckoutPopupController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $data = Website::where('is_archieved', 0)->orderBy('name')->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            $data = Website::where('id', $user->website_id)->where('is_archieved', 0)->orderBy('name')->get();
        } else {
            $data = collect();
        }

        return view('admin.popup.index', compact('data'));
    }

    public function show(string $id)
    {
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only view popups for your own website.');
        }

        $data = CheckoutPopup::where('website_id', $id)->orderByDesc('id')->get();
        $website = Website::findOrFail($id);
        $website_id = (int) $id;

        return view('admin.popup.show', compact('data', 'website_id', 'website'));
    }

    public function create(string $id)
    {
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only create popups for your own website.');
        }

        $website = Website::findOrFail($id);

        return view('admin.popup.create', [
            'id' => (int) $id,
            'website' => $website,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $request->website_id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only create popups for your own website.');
        }

        $validated = $request->validate([
            'website_id' => 'required|integer|exists:websites,id',
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:4096',
            'button_text' => 'nullable|string|max:80',
            'button_url' => 'nullable|url|max:1000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'show_once_per_session' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $popup = new CheckoutPopup();
        $popup->website_id = (int) $validated['website_id'];
        $popup->title = $validated['title'];
        $popup->message = $validated['message'] ?? null;
        $popup->button_text = $validated['button_text'] ?? null;
        $popup->button_url = $validated['button_url'] ?? null;
        $popup->starts_at = $validated['starts_at'] ?? null;
        $popup->ends_at = $validated['ends_at'] ?? null;
        $popup->show_once_per_session = $request->boolean('show_once_per_session');
        $popup->is_active = $request->boolean('is_active');
        $popup->is_archieved = false;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'checkout_popup_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            $popup->image_path = $fileName;
        }

        $popup->save();

        return redirect()->route('admin.popup.show', $popup->website_id)
            ->with('success', 'Checkout popup created successfully.');
    }

    public function edit(string $id)
    {
        $popup = CheckoutPopup::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $popup->website_id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only edit popups for your own website.');
        }

        return view('admin.popup.edit', compact('popup'));
    }

    public function update(Request $request, string $id)
    {
        $popup = CheckoutPopup::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $popup->website_id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only update popups for your own website.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|image|max:4096',
            'button_text' => 'nullable|string|max:80',
            'button_url' => 'nullable|url|max:1000',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after_or_equal:starts_at',
            'show_once_per_session' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $popup->title = $validated['title'];
        $popup->message = $validated['message'] ?? null;
        $popup->button_text = $validated['button_text'] ?? null;
        $popup->button_url = $validated['button_url'] ?? null;
        $popup->starts_at = $validated['starts_at'] ?? null;
        $popup->ends_at = $validated['ends_at'] ?? null;
        $popup->show_once_per_session = $request->boolean('show_once_per_session');
        $popup->is_active = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'checkout_popup_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $fileName);
            $popup->image_path = $fileName;
        }

        $popup->save();

        return redirect()->route('admin.popup.show', $popup->website_id)
            ->with('success', 'Checkout popup updated successfully.');
    }

    public function archive(string $id)
    {
        $popup = CheckoutPopup::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $popup->website_id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only manage popups for your own website.');
        }

        $popup->is_archieved = true;
        $popup->save();

        return back()->with('success', 'Popup archived successfully.');
    }

    public function unarchive(string $id)
    {
        $popup = CheckoutPopup::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && (int) $popup->website_id !== (int) $user->website_id) {
            abort(403, 'Access denied. You can only manage popups for your own website.');
        }

        $popup->is_archieved = false;
        $popup->save();

        return back()->with('success', 'Popup unarchived successfully.');
    }
}
