<?php

namespace App\Http\Controllers;

use App\Models\FeedModel;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FeedModelController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $websiteIds = $this->accessibleWebsiteIds();

        $models = FeedModel::with('website')
            ->whereIn('website_id', $websiteIds)
            ->latest()
            ->get();

        return view('admin.feed-model.index', [
            'models' => $models,
            'websites' => $this->accessibleWebsites(),
            'user' => $user,
        ]);
    }

    public function create(): View
    {
        return view('admin.feed-model.create', [
            'websites' => $this->accessibleWebsites(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'profile_image' => 'nullable|image|max:4096',
            'is_active' => 'nullable|boolean',
        ]);

        $this->ensureWebsiteAccess((int) $validated['website_id']);

        $model = new FeedModel();
        $model->website_id = $validated['website_id'];
        $model->name = $validated['name'];
        $model->bio = $validated['bio'] ?? null;
        $model->is_active = $request->boolean('is_active', true);

        if ($request->hasFile('profile_image')) {
            $model->profile_image = $this->storeImage($request->file('profile_image'), 'feed_model');
        }

        $model->save();

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed model created successfully.');
    }

    public function edit(FeedModel $feedModel): View
    {
        $this->ensureWebsiteAccess($feedModel->website_id);

        return view('admin.feed-model.edit', [
            'feedModel' => $feedModel,
            'websites' => $this->accessibleWebsites(),
        ]);
    }

    public function update(Request $request, FeedModel $feedModel): RedirectResponse
    {
        $validated = $request->validate([
            'website_id' => 'required|exists:websites,id',
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'profile_image' => 'nullable|image|max:4096',
            'is_active' => 'nullable|boolean',
        ]);

        $this->ensureWebsiteAccess($feedModel->website_id);
        $this->ensureWebsiteAccess((int) $validated['website_id']);

        $feedModel->website_id = $validated['website_id'];
        $feedModel->name = $validated['name'];
        $feedModel->bio = $validated['bio'] ?? null;
        $feedModel->is_active = $request->boolean('is_active', true);

        if ($request->hasFile('profile_image')) {
            $feedModel->profile_image = $this->storeImage($request->file('profile_image'), 'feed_model');
        }

        $feedModel->save();

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed model updated successfully.');
    }

    public function destroy(FeedModel $feedModel): RedirectResponse
    {
        $this->ensureWebsiteAccess($feedModel->website_id);
        $feedModel->delete();

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed model deleted successfully.');
    }

    private function accessibleWebsites(): Collection
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return Website::orderBy('name')->get();
        }

        if ($user->isWebsiteUser() && $user->website_id) {
            return Website::where('id', $user->website_id)->orderBy('name')->get();
        }

        abort(403);
    }

    private function accessibleWebsiteIds(): array
    {
        return $this->accessibleWebsites()->pluck('id')->all();
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        if (!in_array($websiteId, $this->accessibleWebsiteIds(), true)) {
            abort(403, 'Access denied for this website.');
        }
    }

    private function storeImage($file, string $prefix): string
    {
        $fileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $fileName);

        return $fileName;
    }
}