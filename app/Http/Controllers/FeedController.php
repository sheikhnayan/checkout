<?php

namespace App\Http\Controllers;

use App\Models\FeedComment;
use App\Models\FeedModel;
use App\Models\FeedModelPerformanceDate;
use App\Models\FeedPost;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $websites = $this->activeWebsitesQuery()
            ->whereHas('feedPosts', function ($query) {
                $query->where('is_active', true)
                    ->where('approval_status', 'approved');
            })
            ->orderBy('name')
            ->get();

        return view('feed.index', [
            'posts' => collect(),
            'websites' => $websites,
            'club' => null,
            'query' => (string) $request->string('q'),
            'dateQuery' => trim((string) $request->input('date', '')),
        ]);
    }

    public function clubFeed(Request $request, string $slug): View
    {
        $club = $this->findActiveClub($slug);

        $queryTerm = trim((string) $request->string('q'));
        $dateQuery = trim((string) $request->input('date', ''));
        $searchDate = $this->extractSearchDate($dateQuery !== '' ? $dateQuery : $queryTerm);

        $posts = $this->visibleClubPostsQuery($club)
            ->with(['website', 'feedModel', 'visibleComments'])
            ->withCount('visibleComments')
            ->when($queryTerm !== '' || $searchDate !== null, function ($query) use ($queryTerm, $searchDate) {
                $query->where(function ($inner) use ($queryTerm, $searchDate) {
                    if ($queryTerm !== '') {
                        $inner->where('caption', 'like', '%' . $queryTerm . '%')
                            ->orWhereHas('feedModel', function ($feedModelQuery) use ($queryTerm) {
                                $feedModelQuery->where('name', 'like', '%' . $queryTerm . '%')
                                    ->orWhere('bio', 'like', '%' . $queryTerm . '%');
                            })
                            ->orWhereHas('website', function ($websiteQuery) use ($queryTerm) {
                                $websiteQuery->where('name', 'like', '%' . $queryTerm . '%')
                                    ->orWhere('description', 'like', '%' . $queryTerm . '%');
                            });
                    }

                    if ($searchDate !== null) {
                        $inner->orWhereDate('posted_at', $searchDate)
                            ->orWhereHas('feedModel.performanceDates', function ($dateQuery) use ($searchDate) {
                                $dateQuery->whereDate('performance_date', $searchDate);
                            });
                    }
                });
            })
            ->latest('posted_at')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('feed.index', [
            'posts' => $posts,
            'websites' => collect([$club]),
            'club' => $club,
            'query' => $queryTerm,
            'dateQuery' => $dateQuery,
        ]);
    }

    public function clubProfile(string $slug): View
    {
        $club = $this->findActiveClub($slug);

        $posts = $this->visibleClubPostsQuery($club)
            ->where('author_mode', 'club')
            ->with(['website', 'feedModel', 'visibleComments'])
            ->withCount('visibleComments')
            ->latest('posted_at')
            ->latest()
            ->get();

        $models = FeedModel::where('website_id', $club->id)
            ->where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('is_active', true)
                    ->where('approval_status', 'approved');
            }])
            ->orderBy('name')
            ->get();

        return view('feed.profile', [
            'club' => $club,
            'profileType' => 'club',
            'profileEntity' => $club,
            'profileTitle' => $club->name,
            'profileSubtitle' => $club->hero_subtitle ?: $club->description,
            'profileImage' => $club->logo,
            'posts' => $posts,
            'models' => $models,
            'activeModel' => null,
            'performanceDates' => collect(),
            'rollCallDefaultDate' => now()->format('Y-m-d'),
        ]);
    }

    public function clubRollCall(Request $request, string $slug): View
    {
        $club = $this->findActiveClub($slug);

        $requestedDate = trim((string) $request->input('date', ''));
        $selectedDate = now()->format('Y-m-d');

        if ($requestedDate !== '') {
            try {
                $selectedDate = Carbon::parse($requestedDate)->format('Y-m-d');
            } catch (\Throwable $e) {
                $selectedDate = now()->format('Y-m-d');
            }
        }

        $availableDates = FeedModelPerformanceDate::query()
            ->whereHas('feedModel', function ($query) use ($club) {
                $query->where('website_id', $club->id)
                    ->where('is_active', true);
            })
            ->orderBy('performance_date')
            ->get()
            ->map(function ($item) {
                return optional($item->performance_date)->format('Y-m-d');
            })
            ->filter()
            ->unique()
            ->values();

        $workingModels = FeedModel::query()
            ->where('website_id', $club->id)
            ->where('is_active', true)
            ->whereHas('performanceDates', function ($query) use ($selectedDate) {
                $query->whereDate('performance_date', $selectedDate);
            })
            ->with(['performanceDates' => function ($query) use ($selectedDate) {
                $query->whereDate('performance_date', $selectedDate)
                    ->orderBy('performance_date');
            }])
            ->orderBy('name')
            ->get();

        $eventPosts = $this->visibleClubPostsQuery($club)
            ->where('show_on_roll_call', true)
            ->where(function ($query) use ($selectedDate) {
                $query->whereDate('roll_call_date', $selectedDate)
                    ->orWhere(function ($rangeQuery) use ($selectedDate) {
                        $rangeQuery->whereNotNull('roll_call_start_date')
                            ->whereNotNull('roll_call_end_date')
                            ->whereDate('roll_call_start_date', '<=', $selectedDate)
                            ->whereDate('roll_call_end_date', '>=', $selectedDate);
                    });
            })
            ->with(['website', 'feedModel', 'visibleComments'])
            ->withCount('visibleComments')
            ->latest('posted_at')
            ->latest()
            ->get();

        return view('feed.roll-call', [
            'club' => $club,
            'selectedDate' => $selectedDate,
            'availableDates' => $availableDates,
            'workingModels' => $workingModels,
            'eventPosts' => $eventPosts,
        ]);
    }

    public function modelProfile(string $slug, FeedModel $feedModel): View
    {
        $club = $this->findActiveClub($slug);

        abort_unless(
            (int) $feedModel->website_id === (int) $club->id && $feedModel->is_active,
            404
        );

        $feedModel->load(['performanceDates' => function ($query) {
            $query->orderBy('performance_date');
        }]);

        $posts = $this->visibleClubPostsQuery($club)
            ->where('author_mode', 'model')
            ->where('feed_model_id', $feedModel->id)
            ->with(['website', 'feedModel', 'visibleComments'])
            ->withCount('visibleComments')
            ->latest('posted_at')
            ->latest()
            ->get();

        $models = FeedModel::where('website_id', $club->id)
            ->where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('is_active', true)
                    ->where('approval_status', 'approved');
            }])
            ->orderBy('name')
            ->get();

        return view('feed.profile', [
            'club' => $club,
            'profileType' => 'model',
            'profileEntity' => $feedModel,
            'profileTitle' => $feedModel->name,
            'profileSubtitle' => $feedModel->bio,
            'profileImage' => $feedModel->profile_image,
            'posts' => $posts,
            'models' => $models,
            'activeModel' => $feedModel,
            'performanceDates' => $this->formatPerformanceDates($feedModel),
            'rollCallDefaultDate' => now()->format('Y-m-d'),
        ]);
    }

    public function storeComment(Request $request, FeedPost $feedPost): RedirectResponse
    {
        abort_if(!$feedPost->is_active || (string) $feedPost->approval_status !== 'approved', 404);

        $validated = $request->validate([
            'commenter_name' => 'required|string|max:255',
            'commenter_email' => 'nullable|email|max:255',
            'body' => 'required|string|max:2000',
            'comment_hp' => 'nullable|string|max:255',
            'comment_form_ts' => 'nullable|integer',
        ]);

        $commenterName = $this->normalizeCommentValue($validated['commenter_name']);
        $commentBody = $this->normalizeCommentValue($validated['body']);

        $this->guardCommentSubmission($request, $feedPost, $commenterName, $commentBody);

        $feedPost->comments()->create([
            'commenter_name' => $commenterName,
            'commenter_email' => $validated['commenter_email'] ?? null,
            'body' => $commentBody,
            'ip_address' => $request->ip(),
            'is_visible' => true,
        ]);

        return redirect()
            ->route('club.feed', [
                'slug' => $feedPost->website->slug,
                'q' => $request->input('q'),
            ])
            ->with('success', 'Comment posted successfully.')
            ->withFragment('post-' . $feedPost->id);
    }

    private function guardCommentSubmission(Request $request, FeedPost $feedPost, string $commenterName, string $commentBody): void
    {
        if ($request->filled('comment_hp')) {
            throw ValidationException::withMessages([
                'body' => 'Unable to post comment.',
            ]);
        }

        $formTimestamp = (int) $request->input('comment_form_ts');
        if ($formTimestamp > 0 && now()->timestamp - $formTimestamp < 2) {
            throw ValidationException::withMessages([
                'body' => 'Please wait a moment before posting your comment.',
            ]);
        }

        if (mb_strlen($commentBody) < 3) {
            throw ValidationException::withMessages([
                'body' => 'Comment is too short.',
            ]);
        }

        $ipAddress = (string) $request->ip();
        $minuteRateKey = 'feed-comment:minute:' . sha1($feedPost->id . '|' . $ipAddress);
        $hourRateKey = 'feed-comment:hour:' . sha1($feedPost->id . '|' . $ipAddress);

        if (RateLimiter::tooManyAttempts($minuteRateKey, 3) || RateLimiter::tooManyAttempts($hourRateKey, 12)) {
            throw ValidationException::withMessages([
                'body' => 'Too many comments from your connection. Please wait before trying again.',
            ]);
        }

        RateLimiter::hit($minuteRateKey, 60);
        RateLimiter::hit($hourRateKey, 3600);

        $combinedText = $commenterName . ' ' . $commentBody;

        if ($this->containsLinkLikeContent($combinedText)) {
            throw ValidationException::withMessages([
                'body' => 'Links and website addresses are not allowed in comments.',
            ]);
        }

        if ($this->containsEmailLikeContent($combinedText)) {
            throw ValidationException::withMessages([
                'body' => 'Email addresses are not allowed in comments.',
            ]);
        }

        if ($this->containsPhoneLikeContent($combinedText)) {
            throw ValidationException::withMessages([
                'body' => 'Contact details are not allowed in comments.',
            ]);
        }

        if ($this->containsAbusiveLanguage($combinedText)) {
            throw ValidationException::withMessages([
                'body' => 'Please keep comments respectful.',
            ]);
        }

        if ($this->looksLikeSpam($commenterName, $commentBody)) {
            throw ValidationException::withMessages([
                'body' => 'Your comment looks like spam and could not be posted.',
            ]);
        }

        $isDuplicate = FeedComment::query()
            ->where('feed_post_id', $feedPost->id)
            ->where('ip_address', $ipAddress)
            ->where('body', $commentBody)
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($isDuplicate) {
            throw ValidationException::withMessages([
                'body' => 'Duplicate comments are not allowed.',
            ]);
        }
    }

    private function normalizeCommentValue(string $value): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value) ?? '');

        return $value;
    }

    private function containsLinkLikeContent(string $value): bool
    {
        return (bool) preg_match(
            '/(?:https?:\/\/|www\.|\b[a-z0-9][a-z0-9\-]{1,62}\.(?:com|net|org|io|co|me|ly|info|biz|app|gg|tv|us|uk|ca|au|in|de|fr|nl|xyz)\b|\b(?:dot)\s+(?:com|net|org|io|co|me|ly|info|biz|app)\b)/iu',
            $value
        );
    }

    private function containsEmailLikeContent(string $value): bool
    {
        return (bool) preg_match('/[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}/iu', $value);
    }

    private function containsPhoneLikeContent(string $value): bool
    {
        return (bool) preg_match('/(?:\+?\d[\d\s().\-]{7,}\d|\b(?:whatsapp|telegram|snapchat|contact me|text me|dm me|inbox me)\b)/iu', $value);
    }

    private function containsAbusiveLanguage(string $value): bool
    {
        return (bool) preg_match('/\b(?:fuck|f\*+k|shit|bitch|asshole|bastard|slut|whore|dick|cunt|motherfucker)\b/iu', $value);
    }

    private function looksLikeSpam(string $commenterName, string $commentBody): bool
    {
        $combined = $commenterName . ' ' . $commentBody;

        if (preg_match('/(.)\1{6,}/u', $combined)) {
            return true;
        }

        if (preg_match('/\b(\p{L}{3,})\b(?:\s+\1\b){3,}/iu', $combined)) {
            return true;
        }

        if (preg_match('/\b(?:earn money|work from home|crypto|forex|investment|loan approval|seo service|marketing service|buy followers|cheap traffic)\b/iu', $combined)) {
            return true;
        }

        $lettersOnly = preg_replace('/[^\p{L}]+/u', '', $combined) ?? '';
        if ($lettersOnly !== '' && mb_strlen($lettersOnly) < (int) floor(mb_strlen($combined) * 0.35)) {
            return true;
        }

        return false;
    }

    private function activeWebsitesQuery()
    {
        return Website::where('status', 1)
            ->where(function ($query) {
                $query->whereNull('is_archieved')->orWhere('is_archieved', 0);
            });
    }

    private function findActiveClub(string $slug): Website
    {
        return $this->activeWebsitesQuery()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    private function visibleClubPostsQuery(Website $club)
    {
        return FeedPost::query()
            ->where('website_id', $club->id)
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->where(function ($query) {
                $query->where('author_mode', 'club')
                    ->orWhereHas('feedModel', function ($feedModelQuery) {
                        $feedModelQuery->where('is_active', true);
                    });
            });
    }

    private function formatPerformanceDates(FeedModel $feedModel)
    {
        $today = now()->startOfDay();

        $mapped = $feedModel->performanceDates->map(function ($performanceDate) {
            $rawDate = trim((string) optional($performanceDate->performance_date)->format('Y-m-d'));

            try {
                $parsedDate = Carbon::parse($rawDate)->startOfDay();
            } catch (\Throwable $e) {
                $parsedDate = null;
            }

            return [
                'raw_date' => $rawDate,
                'parsed_date' => $parsedDate,
            ];
        });

        $upcoming = $mapped
            ->filter(fn ($item) => $item['parsed_date'] !== null && $item['parsed_date']->greaterThanOrEqualTo($today))
            ->sortBy(fn ($item) => $item['parsed_date']->timestamp)
            ->values();

        return $upcoming->take(10)->map(function ($item) {
            $parsedDate = $item['parsed_date'];

            return [
                'short' => $parsedDate ? $parsedDate->format('D, M j') : ($item['raw_date'] ?: 'Date TBA'),
                'full' => $parsedDate ? $parsedDate->format('l, F j, Y') : ($item['raw_date'] ?: 'Date TBA'),
            ];
        })->values();
    }

    private function extractSearchDate(string $queryTerm): ?string
    {
        if ($queryTerm === '') {
            return null;
        }

        $formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'M j, Y', 'F j, Y', 'M j', 'F j'];

        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $queryTerm);
                if ($parsed !== false) {
                    if (!str_contains($format, 'Y')) {
                        $parsed->year = now()->year;
                    }

                    return $parsed->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // Try next date format.
            }
        }

        try {
            return Carbon::parse($queryTerm)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}