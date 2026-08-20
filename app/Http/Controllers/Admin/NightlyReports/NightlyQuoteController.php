<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrQuote;

class NightlyQuoteController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $quotes = NrQuote::orderBy('sort_order')->get();
        return view('admin.nightly-reports.quotes.index', compact('quotes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'quote_text' => 'required|string',
            'author' => 'required|string|max:100',
            'category' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
        ]);

        NrQuote::create($validated);
        return back()->with('success', 'Quote added successfully.');
    }

    public function update(Request $request, $id)
    {
        $quote = NrQuote::findOrFail($id);
        $validated = $request->validate([
            'quote_text' => 'required|string',
            'author' => 'required|string|max:100',
            'category' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);

        $quote->update($validated);
        return back()->with('success', 'Quote updated.');
    }

    public function destroy($id)
    {
        NrQuote::findOrFail($id)->delete();
        return back()->with('success', 'Quote removed.');
    }
}
