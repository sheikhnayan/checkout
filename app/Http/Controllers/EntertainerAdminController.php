<?php

namespace App\Http\Controllers;

use App\Mail\EntertainerApprovedMail;
use App\Models\Entertainer;
use App\Models\FeedModel;
use App\Models\SMTP;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EntertainerAdminController extends Controller
{
    private function currentUser()
    {
        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isWebsiteUser())) {
            abort(403, 'Only super admin or club admin can manage entertainers.');
        }

        return $user;
    }

    private function scopeQueryForUser($query)
    {
        $user = $this->currentUser();

        if ($user->isWebsiteUser()) {
            $query->where('website_id', (int) $user->website_id);
        }

        return $query;
    }

    private function ensureCanManageEntertainer(Entertainer $entertainer): void
    {
        $user = $this->currentUser();
        if ($user->isAdmin()) {
            return;
        }

        if ((int) $user->website_id !== (int) $entertainer->website_id) {
            abort(403, 'You can only manage entertainers from your own club.');
        }
    }

    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = Entertainer::with(['user', 'website']);
        $this->scopeQueryForUser($query);

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $entertainers = $query->latest()->get();

        $currentUser = auth()->user();
        $shareClub = null;
        if ($currentUser && $currentUser->isWebsiteUser() && $currentUser->website_id) {
            $shareClub = Website::find($currentUser->website_id);
        }

        return view('admin.entertainer.index', compact('entertainers', 'status', 'shareClub'));
    }

    public function show(Entertainer $entertainer)
    {
        $this->ensureCanManageEntertainer($entertainer);
        $entertainer->load(['user', 'website', 'feedModel']);

        return view('admin.entertainer.show', compact('entertainer'));
    }

    public function approve(Entertainer $entertainer)
    {
        $this->ensureCanManageEntertainer($entertainer);

        request()->validate([
            'default_commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $entertainer->status = 'approved';
        $entertainer->is_active = true;
        $entertainer->approved_at = now();
        $entertainer->approved_by = auth()->id();
        $entertainer->default_commission_percentage = request('default_commission_percentage');

        if (empty($entertainer->slug)) {
            $entertainer->slug = Entertainer::generateUniqueSlug($entertainer->display_name ?: $entertainer->user->name);
        }

        if (!$entertainer->feed_model_id) {
            $feedModel = FeedModel::create([
                'website_id' => $entertainer->website_id,
                'name' => $entertainer->display_name ?: $entertainer->user->name,
                'profile_image' => $entertainer->profile_image,
                'bio' => $entertainer->description,
                'is_real_profile' => true,
                'is_active' => true,
            ]);
            $entertainer->feed_model_id = $feedModel->id;
        } else {
            $feedModel = FeedModel::find($entertainer->feed_model_id);
            if ($feedModel) {
                $feedModel->name = $entertainer->display_name ?: $entertainer->user->name;
                $feedModel->website_id = $entertainer->website_id;
                if (!empty($entertainer->profile_image)) {
                    $feedModel->profile_image = $entertainer->profile_image;
                }
                $feedModel->bio = $entertainer->description;
                $feedModel->is_real_profile = true;
                $feedModel->is_active = true;
                $feedModel->save();
            }
        }

        $entertainer->save();

        try {
            $this->applyGlobalSmtp();
            Mail::to($entertainer->user->email)->send(new EntertainerApprovedMail($entertainer));
        } catch (\Throwable $th) {
            // Keep approval successful even if mail fails.
        }

        return redirect()->back()->with('success', 'Entertainer approved successfully.');
    }

    public function updateCommission(Request $request, Entertainer $entertainer)
    {
        $this->ensureCanManageEntertainer($entertainer);

        $request->validate([
            'default_commission_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $entertainer->default_commission_percentage = $request->default_commission_percentage;
        $entertainer->save();

        return redirect()->back()->with('success', 'Entertainer commission updated successfully.');
    }

    public function reject(Request $request, Entertainer $entertainer)
    {
        $this->ensureCanManageEntertainer($entertainer);

        $request->validate([
            'rejection_reason' => 'nullable|string|max:1000',
        ]);

        $entertainer->status = 'rejected';
        $entertainer->rejection_reason = $request->rejection_reason;
        $entertainer->is_active = false;
        $entertainer->save();

        if ($entertainer->feed_model_id) {
            FeedModel::where('id', $entertainer->feed_model_id)->update([
                'is_active' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Entertainer application rejected.');
    }

    public function unapprove(Entertainer $entertainer)
    {
        $this->ensureCanManageEntertainer($entertainer);

        $entertainer->status = 'pending';
        $entertainer->is_active = false;
        $entertainer->approved_at = null;
        $entertainer->approved_by = null;
        $entertainer->rejection_reason = null;
        $entertainer->save();

        if ($entertainer->feed_model_id) {
            FeedModel::where('id', $entertainer->feed_model_id)->update([
                'is_active' => false,
            ]);
        }

        return redirect()->back()->with('success', 'Entertainer has been unapproved and moved back to pending review.');
    }

    private function applyGlobalSmtp(): void
    {
        $smtp = SMTP::latest()->first();
        if (!$smtp || empty($smtp->host) || empty($smtp->port) || empty($smtp->username) || empty($smtp->password)) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtp->host,
            'mail.mailers.smtp.port' => $smtp->port,
            'mail.mailers.smtp.username' => $smtp->username,
            'mail.mailers.smtp.password' => $smtp->password,
            'mail.mailers.smtp.encryption' => in_array($smtp->encryption, ['tls', 'ssl'], true) ? $smtp->encryption : ((string) $smtp->encryption === '1' ? 'tls' : null),
            'mail.from.address' => $smtp->from_email ?: config('mail.from.address'),
            'mail.from.name' => $smtp->from_name ?: config('mail.from.name'),
        ]);
    }
}
