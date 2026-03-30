<?php

namespace App\Http\Controllers;

use App\Mail\EntertainerApplicationReceivedMail;
use App\Models\Entertainer;
use App\Models\SMTP;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EntertainerRegistrationController extends Controller
{
    public function showForm(Request $request)
    {
        $clubSlug = trim((string) $request->query('club', ''));
        $selectedClub = $clubSlug !== ''
            ? Website::where('slug', $clubSlug)->where('status', 1)->where('is_archieved', 0)->first()
            : null;

        $clubs = Website::where('status', 1)
            ->where('is_archieved', 0)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('entertainer.apply', compact('clubs', 'selectedClub'));
    }

    public function submit(Request $request)
    {
        $clubSlug = trim((string) $request->input('club_slug', $request->query('club', '')));
        $selectedClub = $clubSlug !== ''
            ? Website::where('slug', $clubSlug)->where('status', 1)->where('is_archieved', 0)->first()
            : null;

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:30',
            'experience' => 'nullable|string|max:1000',
        ];

        if (!$selectedClub) {
            $rules['website_id'] = 'required|integer|exists:websites,id';
        }

        $validated = $request->validate($rules);

        $clubId = $selectedClub
            ? (int) $selectedClub->id
            : (int) $validated['website_id'];

        $club = Website::where('id', $clubId)
            ->where('status', 1)
            ->where('is_archieved', 0)
            ->firstOrFail();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'website_id' => $club->id,
            'user_type' => 'entertainer',
        ]);

        $entertainer = Entertainer::create([
            'user_id' => $user->id,
            'website_id' => $club->id,
            'status' => 'pending',
            'slug' => Entertainer::generateUniqueSlug($validated['name']),
            'display_name' => $validated['name'],
            'description' => $validated['experience'] ?? null,
        ]);

        try {
            $this->applyGlobalSmtp();
            Mail::to($user->email)->send(new EntertainerApplicationReceivedMail($entertainer));

            $adminEmails = User::where('user_type', 'admin')->pluck('email')->filter()->toArray();
            foreach ($adminEmails as $adminEmail) {
                Mail::raw(
                    'New entertainer application received from ' . $user->name . ' (' . $user->email . ') for club ' . $club->name . '.',
                    function ($message) use ($adminEmail) {
                        $message->to($adminEmail)->subject('New Entertainer Application - CartVIP');
                    }
                );
            }

            $websiteUserEmails = User::where('user_type', 'website_user')
                ->where('website_id', $club->id)
                ->pluck('email')
                ->filter()
                ->toArray();

            foreach ($websiteUserEmails as $websiteUserEmail) {
                Mail::raw(
                    'A new entertainer application was submitted for your club: ' . $club->name . '.',
                    function ($message) use ($websiteUserEmail) {
                        $message->to($websiteUserEmail)->subject('New Entertainer Application - Club Notification');
                    }
                );
            }
        } catch (\Throwable $th) {
            // Keep registration successful even if mail fails.
        }

        return redirect()->route('login')->with('success', 'Your entertainer application has been submitted successfully. We will review your application and notify you once approved.');
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
