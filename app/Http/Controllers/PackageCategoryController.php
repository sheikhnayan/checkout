<?php

namespace App\Http\Controllers;

use App\Models\PackageCategory;
use App\Models\Website;
use Illuminate\Http\Request;

class PackageCategoryController extends Controller
{
    public function store(Request $request, $websiteId)
    {
        $user = auth()->user();

        if ($user->isWebsiteUser() && $websiteId != $user->website_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ]);

        PackageCategory::firstOrCreate(
            ['website_id' => $websiteId, 'name' => trim($request->name)],
            [
                'sort_order' => (int) $request->input('sort_order', 0),
                'icon' => $request->input('icon') ?: null,
            ]
        );

        return redirect()->route('admin.package.show', $websiteId)
            ->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && $category->website_id != $user->website_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:191',
            'sort_order' => 'nullable|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ]);

        $category->name = trim($request->name);
        $category->sort_order = $request->has('sort_order')
            ? (int) $request->input('sort_order', 0)
            : (int) ($category->sort_order ?? 0);
        if ($request->has('icon')) {
            $category->icon = $request->input('icon') ?: null;
        }
        $category->save();

        return redirect()->route('admin.package.show', $category->website_id)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = PackageCategory::findOrFail($id);
        $user = auth()->user();

        if ($user->isWebsiteUser() && $category->website_id != $user->website_id) {
            abort(403);
        }

        $websiteId = $category->website_id;
        $category->delete();

        return redirect()->route('admin.package.show', $websiteId)
            ->with('success', 'Category deleted.');
    }
}
