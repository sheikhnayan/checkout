<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $recentTransactions = \App\Models\Transaction::latest()->take(5)->get();
            return view('admin.dashboard', compact('recentTransactions'));
        }

        // Website users and managers: scoped dashboard
        $accessibleIds = $user->accessibleWebsiteIds();

        $allocatedWebsites = $user->isManager()
            ? $user->managedWebsites()->orderBy('name')->get()
            : ($user->website ? collect([$user->website]) : collect());

        $scopedEventCount = \App\Models\Event::whereIn('website_id', $accessibleIds)->count();

        $scopedTransactionCount = \App\Models\Transaction::where(function ($q) use ($accessibleIds) {
            $q->whereHas('event', fn($s) => $s->whereIn('website_id', $accessibleIds))
              ->orWhereHas('package', fn($s) => $s->whereIn('website_id', $accessibleIds));
        })->count();

        $recentTransactions = \App\Models\Transaction::where(function ($q) use ($accessibleIds) {
            $q->whereHas('event', fn($s) => $s->whereIn('website_id', $accessibleIds))
              ->orWhereHas('package', fn($s) => $s->whereIn('website_id', $accessibleIds));
        })->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'recentTransactions',
            'allocatedWebsites',
            'scopedEventCount',
            'scopedTransactionCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
