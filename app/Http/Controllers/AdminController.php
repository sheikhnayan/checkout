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
        
        // Get transactions based on user type
        if ($user->isAdmin()) {
            $recentTransactions = \App\Models\Transaction::latest()->take(5)->get();
        } elseif ($user->isWebsiteUser() && $user->website_id) {
            // Website user sees only their website's transactions
            $recentTransactions = \App\Models\Transaction::where(function($query) use ($user) {
                $query->whereHas('event', function($subQuery) use ($user) {
                    $subQuery->where('website_id', $user->website_id);
                })
                ->orWhereHas('package', function($subQuery) use ($user) {
                    $subQuery->where('website_id', $user->website_id);
                });
            })->latest()->take(5)->get();
        } else {
            $recentTransactions = collect();
        }
        
        return view('admin.dashboard', compact('recentTransactions'));
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
