<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Entertainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class StaffAdminController extends Controller
{
    /**
     * Show all current staff registrations (both affiliates and entertainers)
     */
    public function index(Request $request)
    {
        $type = $request->query('type', 'entertainer'); // 'affiliate' or 'entertainer'
        $status = $request->query('status', 'pending'); // 'pending', 'approved', 'rejected'

        if ($type === 'affiliate') {
            $staffList = Affiliate::where('is_staff_registration', true)
                ->where('status', $status)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $pageTitle = 'Current Staff - Promoters';
        } else {
            $staffList = Entertainer::where('is_staff_registration', true)
                ->where('status', $status)
                ->with('user', 'website')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $pageTitle = 'Current Staff - Entertainers';
        }

        return view('admin.staff.index', [
            'staffList' => $staffList,
            'type' => $type,
            'status' => $status,
            'pageTitle' => $pageTitle,
        ]);
    }

    /**
     * Show staff registration details
     */
    public function show($type, $id)
    {
        if ($type === 'affiliate') {
            $staff = Affiliate::where('is_staff_registration', true)->findOrFail($id);
            $staffType = 'Promoter';
        } else {
            $staff = Entertainer::where('is_staff_registration', true)->findOrFail($id);
            $staffType = 'Entertainer';
        }

        $staff->load('user', 'website');

        return view('admin.staff.show', [
            'staff' => $staff,
            'type' => $type,
            'staffType' => $staffType,
        ]);
    }

    /**
     * Approve staff registration
     */
    public function approve($type, $id)
    {
        if ($type === 'affiliate') {
            $staff = Affiliate::where('is_staff_registration', true)->findOrFail($id);
        } else {
            $staff = Entertainer::where('is_staff_registration', true)->findOrFail($id);
        }

        $staff->update(['status' => 'approved']);
        $staff->user->update(['status' => 'approved']);

        // Send approval email
        try {
            Mail::raw(
                "Your {$type} staff application has been approved! You can now log in and access your dashboard.",
                function ($message) use ($staff) {
                    $message->to($staff->user->email)->subject('Application Approved - CartVIP');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Staff approval email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', ucfirst($type) . ' staff approved successfully!');
    }

    /**
     * Reject staff registration
     */
    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        if ($type === 'affiliate') {
            $staff = Affiliate::where('is_staff_registration', true)->findOrFail($id);
        } else {
            $staff = Entertainer::where('is_staff_registration', true)->findOrFail($id);
        }

        $staff->update(['status' => 'rejected']);
        $staff->user->update(['status' => 'rejected']);

        // Send rejection email
        try {
            $reason = $request->input('rejection_reason', 'Your application did not meet our requirements.');
            Mail::raw(
                "We regret to inform you that your {$type} staff application has been rejected.\n\nReason: {$reason}",
                function ($message) use ($staff) {
                    $message->to($staff->user->email)->subject('Application Status - CartVIP');
                }
            );
        } catch (\Exception $e) {
            \Log::error('Staff rejection email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', ucfirst($type) . ' staff rejected.');
    }
}
