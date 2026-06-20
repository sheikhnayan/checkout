<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use Illuminate\Http\Request;

class PackageCategoryController extends Controller
{
    public function store(Request $request, $websiteId)
    {
        $user = auth()->user();

        $this->authorizeWebsiteAccess($websiteId, 'Access denied.');

        $request->validate([
            'name' => 'required|string|max:191',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $category = PackageCategory::firstOrNew([
            'website_id' => $websiteId,
            'name' => trim($request->name),
        ]);

        if (!$category->exists) {
            $nextSortOrder = (int) PackageCategory::where('website_id', $websiteId)
                ->max('sort_order');
            $category->sort_order = $nextSortOrder + 1;
        }

        if ($request->has('icon')) {
            $category->icon = $request->input('icon') ?: null;
        }
        if ($request->has('color')) {
            $category->color = $request->input('color') ?: null;
        }

        $category->is_archieved = 0;
        $category->save();

        return redirect()->route('admin.package.show', $websiteId)
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        $this->authorizeWebsiteAccess($category->website_id, 'Access denied.');

        $request->validate([
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        $category->name = trim($request->name);
        $category->sort_order = $request->has('sort_order')
            ? (int) $request->input('sort_order', 0)
            : (int) ($category->sort_order ?? 0);
        if ($request->has('icon')) {
            $category->icon = $request->input('icon') ?: null;
        }
        if ($request->has('color')) {
            $category->color = $request->input('color') ?: null;
        }
        $category->save();

        return redirect()->route('admin.package.show', $category->website_id)
            ->with('success', 'Category updated successfully.');
    }

    public function archive($id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        $this->authorizeWebsiteAccess($category->website_id, 'Access denied.');

        $category->is_archieved = 1;
        $category->save();

        return redirect()->route('admin.package.show', ['id' => $category->website_id, 'tab' => 'categories'])
            ->with('success', 'Category archived successfully.');
    }

    public function unarchive($id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        $this->authorizeWebsiteAccess($category->website_id, 'Access denied.');

        $category->is_archieved = 0;
        $category->save();

        return redirect()->route('admin.package.show', ['id' => $category->website_id, 'tab' => 'categories'])
            ->with('success', 'Category unarchived successfully.');
    }

    public function destroy($id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        $this->authorizeWebsiteAccess($category->website_id, 'Access denied.');

        $websiteId = $category->website_id;
        $category->delete();

        return redirect()->route('admin.package.show', $websiteId)
            ->with('success', 'Category deleted.');
    }

    public function reorder(Request $request, $websiteId)
    {
        $user = auth()->user();

        $this->authorizeWebsiteAccess((int) $websiteId, 'Access denied.');

        $validated = $request->validate([
            'ordered_ids' => 'required|array|min:1',
            'ordered_ids.*' => 'integer',
        ]);

        $orderedIds = collect($validated['ordered_ids'])
            ->map(fn ($id) => (int) $id)
            ->values();

        $categoryIds = PackageCategory::where('website_id', $websiteId)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allowedIdSet = array_flip($categoryIds);

        foreach ($orderedIds as $index => $categoryId) {
            if (!isset($allowedIdSet[$categoryId])) {
                continue;
            }

            PackageCategory::where('id', $categoryId)
                ->where('website_id', $websiteId)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
