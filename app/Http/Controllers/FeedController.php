<?php

namespace App\Http\Controllers;

use App\Models\FeedModel;
use App\Models\FeedPost;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedController extends Controller
{
    public function index(Request $request): View
    {
        $websites = $this->activeWebsitesQuery()
            ->whereHas('feedPosts', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('feed.index', [
            'posts' => collect(),
            'websites' => $websites,
            'club' => null,
            'query' => (string) $request->string('q'),
        ]);
    }

    public function clubFeed(Request $request, string $slug): View
    {
        $club = $this->findActiveClub($slug);

        $queryTerm = trim((string) $request->string('q'));

        $posts = $this->visibleClubPostsQuery($club)
            ->with(['website', 'feedModel', 'visibleComments'])
            ->withCount('visibleComments')
            ->when($queryTerm !== '', function ($query) use ($queryTerm) {
                $query->where(function ($inner) use ($queryTerm) {
                    $inner->where('caption', 'like', '%' . $queryTerm . '%')
                        ->orWhereHas('feedModel', function ($feedModelQuery) use ($queryTerm) {
                            $feedModelQuery->where('name', 'like', '%' . $queryTerm . '%')
                                ->orWhere('bio', 'like', '%' . $queryTerm . '%');
                        })
                        ->orWhereHas('website', function ($websiteQuery) use ($queryTerm) {
                            $websiteQuery->where('name', 'like', '%' . $queryTerm . '%')
                                ->orWhere('description', 'like', '%' . $queryTerm . '%');
                        });
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
        ]);
    }

    public function clubProfile(string $slug): View
    {
        $club = $this->findActiveClub($slug);

        $posts = $this->visibleClubPostsQuery($club)
            ->where('author_mode', 'club')
            ->with(['website', 'feedModel'])
            ->withCount('visibleComments')
            ->latest('posted_at')
            ->latest()
            ->get();

        $models = FeedModel::where('website_id', $club->id)
            ->where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('is_active', true);
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
        ]);
    }

    public function modelProfile(string $slug, FeedModel $feedModel): View
    {
        $club = $this->findActiveClub($slug);

        abort_unless(
            (int) $feedModel->website_id === (int) $club->id && $feedModel->is_active,
            404
        );

        $posts = $this->visibleClubPostsQuery($club)
            ->where('author_mode', 'model')
            ->where('feed_model_id', $feedModel->id)
            ->with(['website', 'feedModel'])
            ->withCount('visibleComments')
            ->latest('posted_at')
            ->latest()
            ->get();

        $models = FeedModel::where('website_id', $club->id)
            ->where('is_active', true)
            ->withCount(['posts' => function ($query) {
                $query->where('is_active', true);
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
        ]);
    }

    public function storeComment(Request $request, FeedPost $feedPost): RedirectResponse
    {
        abort_if(!$feedPost->is_active, 404);

        $validated = $request->validate([
            'commenter_name' => 'required|string|max:255',
            'commenter_email' => 'nullable|email|max:255',
            'body' => 'required|string|max:2000',
        ]);

        $feedPost->comments()->create([
            'commenter_name' => $validated['commenter_name'],
            'commenter_email' => $validated['commenter_email'] ?? null,
            'body' => $validated['body'],
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
            ->where(function ($query) {
                $query->where('author_mode', 'club')
                    ->orWhereHas('feedModel', function ($feedModelQuery) {
                        $feedModelQuery->where('is_active', true);
                    });
            });
    }
}