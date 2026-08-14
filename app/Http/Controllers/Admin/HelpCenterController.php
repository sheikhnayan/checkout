<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomForm;
use App\Models\HelpCenterCollaborator;
use App\Models\HelpCenterItem;
use App\Models\HelpCenterPage;
use App\Models\HelpCenterSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class HelpCenterController extends Controller
{
    /**
     * Display the Help Center dashboard.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        // Owner's page
        $myPage = HelpCenterPage::where('user_id', $user->id)
            ->with(['sections.items', 'collaborators.user', 'collaborators.inviter'])
            ->first();

        // Shared pages where user is an accepted collaborator
        $sharedCollaborations = HelpCenterCollaborator::where('user_id', $user->id)
            ->where('status', 'accepted')
            ->with(['page.owner', 'page.sections.items'])
            ->get();

        // Pending invitations for this user
        $userEmail = strtolower(trim($user->email));
        $pendingInvitations = HelpCenterCollaborator::where(function($q) use ($user, $userEmail) {
                $q->where('user_id', $user->id)
                  ->orWhereRaw('LOWER(email) = ?', [$userEmail]);
            })
            ->where('status', 'pending')
            ->with(['page.owner', 'inviter'])
            ->get();

        // User's custom forms for quick linking
        $customForms = CustomForm::where('is_active', true)
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'slug']);

        return view('admin.help_center.index', compact('myPage', 'sharedCollaborations', 'pendingInvitations', 'customForms'));
    }

    /**
     * Create or update the owner's Help Center Page.
     */
    public function storeOrUpdate(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            abort(401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'banner_color' => 'nullable|string|max:30',
        ]);

        $page = HelpCenterPage::where('user_id', $user->id)->first();

        if (!$page) {
            $page = new HelpCenterPage();
            $page->user_id = $user->id;
            $page->slug = HelpCenterPage::generateUniqueSlug($request->input('title'));
        }

        $page->title = $request->input('title');
        $page->description = $request->input('description');
        $page->banner_color = $request->input('banner_color', '#4f46e5');
        $page->is_active = $request->has('is_active') ? true : true;
        $page->save();

        return redirect()->route('admin.help-center.builder', $page->id)
            ->with('success', 'Help Center Page saved successfully! You can now organize sections and links.');
    }

    /**
     * Display the Help Center Page Builder interface.
     */
    public function builder($id)
    {
        $user = auth()->user();
        $page = HelpCenterPage::with(['sections.items.customForm', 'collaborators.user', 'collaborators.inviter'])
            ->findOrFail($id);

        if (!$page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $customForms = CustomForm::where('is_active', true)
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'slug']);

        return view('admin.help_center.builder', compact('page', 'customForms'));
    }

    /**
     * Store a new section.
     */
    public function storeSection(Request $request, HelpCenterPage $page)
    {
        $user = auth()->user();
        if (!$page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $maxSort = $page->sections()->max('sort_order') ?? 0;

        $page->sections()->create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->back()->with('success', 'Section created successfully!');
    }

    /**
     * Update a section.
     */
    public function updateSection(Request $request, HelpCenterSection $section)
    {
        $user = auth()->user();
        if (!$section->page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $section->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
        ]);

        return redirect()->back()->with('success', 'Section updated successfully!');
    }

    /**
     * Delete a section.
     */
    public function destroySection(HelpCenterSection $section)
    {
        $user = auth()->user();
        if (!$section->page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $section->delete();
        return redirect()->back()->with('success', 'Section deleted successfully!');
    }

    /**
     * Store a new item (form link or external link) in a section.
     */
    public function storeItem(Request $request, HelpCenterSection $section)
    {
        $user = auth()->user();
        if (!$section->page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $request->validate([
            'type' => 'required|in:form,external',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'custom_form_id' => 'nullable|required_if:type,form|exists:custom_forms,id',
            'url' => 'nullable|required_if:type,external|url',
            'icon' => 'nullable|string|max:100',
        ]);

        $maxSort = $section->items()->max('sort_order') ?? 0;

        $section->items()->create([
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'custom_form_id' => $request->input('type') === 'form' ? $request->input('custom_form_id') : null,
            'url' => $request->input('type') === 'external' ? $request->input('url') : null,
            'icon' => $request->input('icon', 'bx-link'),
            'sort_order' => $maxSort + 1,
        ]);

        return redirect()->back()->with('success', 'Link item added to section successfully!');
    }

    /**
     * Update an item.
     */
    public function updateItem(Request $request, HelpCenterItem $item)
    {
        $user = auth()->user();
        if (!$item->section->page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $request->validate([
            'type' => 'required|in:form,external',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'custom_form_id' => 'nullable|required_if:type,form|exists:custom_forms,id',
            'url' => 'nullable|required_if:type,external|url',
            'icon' => 'nullable|string|max:100',
        ]);

        $item->update([
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'custom_form_id' => $request->input('type') === 'form' ? $request->input('custom_form_id') : null,
            'url' => $request->input('type') === 'external' ? $request->input('url') : null,
            'icon' => $request->input('icon', 'bx-link'),
        ]);

        return redirect()->back()->with('success', 'Link item updated successfully!');
    }

    /**
     * Delete an item.
     */
    public function destroyItem(HelpCenterItem $item)
    {
        $user = auth()->user();
        if (!$item->section->page->canUserEdit($user)) {
            abort(403, 'You do not have permission to edit this Help Center page.');
        }

        $item->delete();
        return redirect()->back()->with('success', 'Item deleted successfully!');
    }

    /**
     * Invite a collaborator by email (must be a registered CartVIP user).
     */
    public function inviteCollaborator(Request $request, HelpCenterPage $page)
    {
        $user = auth()->user();
        if ($page->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Only the owner can invite collaborators to this Help Center page.');
        }

        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->input('email')));

        // Check if email belongs to owner
        if (strtolower($user->email) === $email) {
            return redirect()->back()->with('error', 'You are already the owner of this page.');
        }

        // Must be an existing registered user under CartVIP
        $targetUser = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (!$targetUser) {
            return redirect()->back()->with('error', 'The email "' . $email . '" does not belong to a registered CartVIP user. Only existing CartVIP users can be invited.');
        }

        // Check if already invited or active
        $existing = HelpCenterCollaborator::where('help_center_page_id', $page->id)
            ->where(function($q) use ($targetUser, $email) {
                $q->where('user_id', $targetUser->id)
                  ->orWhere('email', $email);
            })
            ->first();

        if ($existing) {
            if ($existing->status === 'accepted') {
                return redirect()->back()->with('error', 'This user is already an active collaborator on this page.');
            } else if ($existing->status === 'pending') {
                return redirect()->back()->with('error', 'An invitation has already been sent to this user.');
            }
        }

        $token = Str::random(40);

        $collaborator = HelpCenterCollaborator::create([
            'help_center_page_id' => $page->id,
            'user_id' => $targetUser->id,
            'invited_by_user_id' => $user->id,
            'email' => $email,
            'status' => 'pending',
            'invitation_token' => $token,
        ]);

        // Send Email Notification
        try {
            $inviteUrl = route('help-center.invitation.accept', $token);
            $subject = "Collaboration Invitation: " . $page->title . " Help Center";
            $html = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f8fafc; border-radius: 8px;'>
                    <h2 style='color: #4f46e5; margin-top: 0;'>Help Center Collaboration Request</h2>
                    <p style='font-size: 15px; color: #334155;'>Hello <strong>" . e($targetUser->name) . "</strong>,</p>
                    <p style='font-size: 15px; color: #334155;'><strong>" . e($user->name) . "</strong> has invited you to collaborate on their Help Center page: <strong>" . e($page->title) . "</strong>.</p>
                    <p style='font-size: 14px; color: #64748b;'>As a collaborator, you will be able to add, edit, and organize sections and links on this Help Center page.</p>
                    <div style='margin: 24px 0; text-align: center;'>
                        <a href='" . $inviteUrl . "' style='background: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Accept Collaboration Invitation</a>
                    </div>
                    <p style='font-size: 12px; color: #94a3b8; margin-top: 20px;'>If you did not expect this invitation, you can safely ignore this email.</p>
                </div>
            ";
            Mail::html($html, function($msg) use ($email, $subject) {
                $msg->to($email)->subject($subject);
            });
        } catch (\Exception $e) {
            // Log mail error silently
        }

        return redirect()->back()->with('success', 'Invitation successfully sent to ' . $email . '!');
    }

    /**
     * Accept a collaborator invitation.
     */
    public function acceptInvitation($token)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $invite = HelpCenterCollaborator::where('invitation_token', $token)->first();
        if (!$invite) {
            return redirect()->route('admin.help-center.index')->with('error', 'Invalid or expired invitation token.');
        }

        $inviteEmail = strtolower(trim($invite->email));
        $userEmail = strtolower(trim($user->email));

        if ($inviteEmail !== $userEmail && (int)$invite->user_id !== (int)$user->id) {
            return redirect()->route('admin.help-center.index')->with('error', 'This invitation was sent to ' . $invite->email . '. You are currently logged in as ' . $user->email . '.');
        }

        $invite->status = 'accepted';
        $invite->user_id = $user->id;
        $invite->save();

        return redirect()->route('admin.help-center.builder', $invite->help_center_page_id)
            ->with('success', 'Invitation accepted! You are now a collaborator on "' . ($invite->page->title ?? 'Help Center') . '".');
    }

    /**
     * Decline a collaborator invitation.
     */
    public function declineInvitation($token)
    {
        $user = auth()->user();
        if (!$user) abort(401);

        $invite = HelpCenterCollaborator::where('invitation_token', $token)->first();
        if ($invite) {
            $invite->status = 'declined';
            $invite->save();
        }

        return redirect()->route('admin.help-center.index')->with('info', 'Invitation declined.');
    }

    /**
     * Remove a collaborator from a Help Center Page.
     */
    public function removeCollaborator(HelpCenterCollaborator $collaborator)
    {
        $user = auth()->user();
        $page = $collaborator->page;

        if ($page->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403, 'Only the owner can remove collaborators.');
        }

        $collaborator->delete();
        return redirect()->back()->with('success', 'Collaborator removed successfully.');
    }

    /**
     * Public Help Center Viewer (Requires CartVIP Login Boundary).
     * Route: GET /help-center/{slug}
     */
    public function publicShow($slug)
    {
        $user = auth()->user();
        if (!$user) {
            // Protected by auth middleware, but fallback safety
            return redirect()->route('login');
        }

        $page = HelpCenterPage::where('slug', $slug)
            ->where('is_active', true)
            ->with(['owner', 'sections.items.customForm', 'acceptedCollaborators.user'])
            ->firstOrFail();

        return view('help_center.public_show', compact('page', 'user'));
    }
}
