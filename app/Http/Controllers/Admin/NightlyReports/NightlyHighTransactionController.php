<?php

namespace App\Http\Controllers\Admin\NightlyReports;

use Illuminate\Http\Request;
use App\Models\NightlyReports\NrHighTransaction;
use App\Models\NightlyReports\NrLocation;

class NightlyHighTransactionController extends BaseNightlyReportsController
{
    public function index(Request $request)
    {
        $locations = $this->accessibleLocations();
        $allowedLocationIds = $this->accessibleLocationIds();

        $selectedLocationId = $request->input('location_id');
        $search = $request->input('search');

        $query = NrHighTransaction::with('location')
            ->whereIn('location_id', $allowedLocationIds);

        if ($selectedLocationId && in_array((int) $selectedLocationId, $allowedLocationIds, true)) {
            $query->where('location_id', (int) $selectedLocationId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('card_last4', 'like', "%{$search}%")
                    ->orWhere('authorizing_manager_name', 'like', "%{$search}%");
            });
        }

        $transactions = $query->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totalHighVol = (float) (clone $query)->sum('amount');

        return view('admin.nightly-reports.high-transactions.index', compact(
            'transactions',
            'locations',
            'selectedLocationId',
            'search',
            'totalHighVol'
        ));
    }

    public function show($id)
    {
        $allowedLocationIds = $this->accessibleLocationIds();
        $txn = NrHighTransaction::with('location')->whereIn('location_id', $allowedLocationIds)->findOrFail($id);

        return response()->json($txn);
    }
}
