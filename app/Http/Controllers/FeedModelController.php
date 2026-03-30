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
    public function index(Request $request): View
    {
        $user = auth()->user();
        $websiteIds = $this->accessibleWebsiteIds();

        $models = FeedModel::with('website')
            ->whereIn('website_id', $websiteIds)
            ->when($request->filled('website_id'), function ($query) use ($request, $websiteIds) {
                $websiteId = (int) $request->input('website_id');

                if (in_array($websiteId, $websiteIds, true)) {
                    $query->where('website_id', $websiteId);
                }
            })
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
            'performance_dates' => 'nullable|array',
            'performance_dates.*' => 'nullable|date',
        ]);

        $this->ensureWebsiteAccess((int) $validated['website_id']);
        $performanceDates = $this->normalizedPerformanceDates($validated['performance_dates'] ?? []);

        $model = new FeedModel();
        $model->website_id = $validated['website_id'];
        $model->name = $validated['name'];
        $model->bio = $validated['bio'] ?? null;
        $model->is_active = $request->boolean('is_active');

        if ($request->hasFile('profile_image')) {
            $model->profile_image = $this->storeImage($request->file('profile_image'), 'feed_model');
        }

        $model->save();
        $model->performanceDates()->createMany(
            collect($performanceDates)->map(fn ($date) => ['performance_date' => $date])->all()
        );

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed entertainer created successfully.');
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
            'performance_dates' => 'nullable|array',
            'performance_dates.*' => 'nullable|date',
        ]);

        $this->ensureWebsiteAccess($feedModel->website_id);
        $this->ensureWebsiteAccess((int) $validated['website_id']);
        $performanceDates = $this->normalizedPerformanceDates($validated['performance_dates'] ?? []);

        $feedModel->website_id = $validated['website_id'];
        $feedModel->name = $validated['name'];
        $feedModel->bio = $validated['bio'] ?? null;
        $feedModel->is_active = $request->boolean('is_active');

        if ($request->hasFile('profile_image')) {
            $feedModel->profile_image = $this->storeImage($request->file('profile_image'), 'feed_model');
        }

        $feedModel->save();
        $feedModel->performanceDates()->delete();
        $feedModel->performanceDates()->createMany(
            collect($performanceDates)->map(fn ($date) => ['performance_date' => $date])->all()
        );

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed entertainer updated successfully.');
    }

    public function destroy(FeedModel $feedModel): RedirectResponse
    {
        $this->ensureWebsiteAccess($feedModel->website_id);
        $feedModel->delete();

        return redirect()->route('admin.feed-model.index')->with('success', 'Feed entertainer deleted successfully.');
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

    private function normalizedPerformanceDates(array $performanceDates): array
    {
        return collect($performanceDates)
            ->map(fn ($date) => trim((string) $date))
            ->filter(fn ($date) => $date !== '')
            ->map(fn ($date) => date('Y-m-d', strtotime($date)))
            ->unique()
            ->sort()
            ->all();
    }

    private function storeImage($file, string $prefix): string
    {
        $fileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $fileName);

        return $fileName;
    }
}