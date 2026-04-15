<?php

namespace App\Http\Controllers;

use App\Models\FeedComment;
use App\Models\FeedModel;
use App\Models\FeedPost;
use App\Models\Website;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class FeedPostController extends Controller
{
    public function index(Request $request): View
    {
        $websiteIds = $this->accessibleWebsiteIds();
        $entertainer = $this->approvedEntertainer();

        $posts = FeedPost::with(['website', 'feedModel'])
            ->withCount('comments')
            ->whereIn('website_id', $websiteIds)
            ->when($entertainer, function ($query) use ($entertainer) {
                $query->where('author_mode', 'model')
                    ->where('feed_model_id', $entertainer->feed_model_id);
            })
            ->when($request->filled('website_id'), function ($query) use ($request, $websiteIds) {
                $id = (int) $request->input('website_id');
                if (in_array($id, $websiteIds)) {
                    $query->where('website_id', $id);
                }
            })
            ->orderByDesc('review_required')
            ->orderByRaw("CASE WHEN approval_status = 'pending' THEN 1 ELSE 0 END DESC")
            ->latest('posted_at')
            ->latest()
            ->get();

        return view('admin.feed-post.index', [
            'posts' => $posts,
            'websites' => $this->accessibleWebsites(),
        ]);
    }

    public function create(): View
    {
        return view('admin.feed-post.create', [
            'websites' => $this->accessibleWebsites(),
            'feedModels' => $this->accessibleFeedModels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePost($request);
        $this->ensureWebsiteAccess((int) $validated['website_id']);
        $this->ensureAuthorSelection($validated);

        $post = new FeedPost();
        $this->fillPost($post, $request, $validated);
        $post->save();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post created successfully.');
    }

    public function show(FeedPost $feedPost): View
    {
        $this->ensurePostManagementAccess($feedPost);
        $feedPost->load(['website', 'feedModel', 'comments']);

        return view('admin.feed-post.show', [
            'feedPost' => $feedPost,
        ]);
    }

    public function edit(FeedPost $feedPost): View
    {
        $this->ensurePostManagementAccess($feedPost);

        return view('admin.feed-post.edit', [
            'feedPost' => $feedPost,
            'websites' => $this->accessibleWebsites(),
            'feedModels' => $this->accessibleFeedModels(),
        ]);
    }

    public function update(Request $request, FeedPost $feedPost): RedirectResponse
    {
        $validated = $this->validatePost($request);
        $this->ensurePostManagementAccess($feedPost);
        $this->ensureWebsiteAccess((int) $validated['website_id']);
        $this->ensureAuthorSelection($validated);

        $this->fillPost($feedPost, $request, $validated);
        $feedPost->save();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post updated successfully.');
    }

    public function destroy(FeedPost $feedPost): RedirectResponse
    {
        $this->ensurePostManagementAccess($feedPost);
        $feedPost->delete();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post deleted successfully.');
    }

    public function toggleCommentVisibility(FeedPost $feedPost, FeedComment $feedComment): RedirectResponse
    {
        $this->ensurePostManagementAccess($feedPost);

        if ($feedComment->feed_post_id !== $feedPost->id) {
            abort(404);
        }

        $feedComment->is_visible = !$feedComment->is_visible;
        $feedComment->save();

        return redirect()->route('admin.feed-post.show', $feedPost)->with('success', 'Comment visibility updated.');
    }

    public function destroyComment(FeedPost $feedPost, FeedComment $feedComment): RedirectResponse
    {
        $this->ensurePostManagementAccess($feedPost);

        if ($feedComment->feed_post_id !== $feedPost->id) {
            abort(404);
        }

        $feedComment->delete();

        return redirect()->route('admin.feed-post.show', $feedPost)->with('success', 'Comment deleted successfully.');
    }

    public function approve(FeedPost $feedPost): RedirectResponse
    {
        $this->ensureApprovePermission($feedPost);

        $feedPost->approval_status = 'approved';
        $feedPost->review_required = false;
        $feedPost->reviewed_at = now();
        $feedPost->approved_at = now();
        $feedPost->approved_by = auth()->id();
        $feedPost->is_active = true;
        $feedPost->save();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post reviewed and approved.');
    }

    public function reject(FeedPost $feedPost): RedirectResponse
    {
        $this->ensureApprovePermission($feedPost);

        $feedPost->approval_status = 'rejected';
        $feedPost->review_required = false;
        $feedPost->reviewed_at = now();
        $feedPost->approved_at = null;
        $feedPost->approved_by = null;
        $feedPost->is_active = false;
        $feedPost->save();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post unapproved and removed from the live feed.');
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $postIds = collect((array) $request->input('feed_post_ids', []))
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($postIds->isEmpty()) {
            return redirect()->route('admin.feed-post.index')->with('error', 'Select at least one post to mark as reviewed.');
        }

        $posts = FeedPost::whereIn('id', $postIds)->get();

        if ($posts->isEmpty()) {
            return redirect()->route('admin.feed-post.index')->with('error', 'No matching feed posts were found.');
        }

        foreach ($posts as $post) {
            $this->ensureApprovePermission($post);

            $post->approval_status = 'approved';
            $post->review_required = false;
            $post->reviewed_at = now();
            $post->approved_at = $post->approved_at ?: now();
            $post->approved_by = $post->approved_by ?: auth()->id();
            $post->is_active = true;
            $post->save();
        }

        return redirect()->route('admin.feed-post.index')->with('success', $posts->count() . ' feed post(s) marked as reviewed.');
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'website_id' => 'required|exists:websites,id',
            'author_mode' => 'required|in:model,club',
            'feed_model_id' => 'nullable|exists:feed_models,id',
            'caption' => 'nullable|string|max:10000',
            'media_uploads' => 'nullable|array|max:12',
            'media_uploads.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,ogg|max:4096',
            'external_media_links' => 'nullable|array|max:20',
            'external_media_links.*' => 'nullable|url|max:2000',
            'external_media_types' => 'nullable|array|max:20',
            'external_media_types.*' => 'nullable|in:image,video',
            'posted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
            'show_on_roll_call' => 'nullable|boolean',
            'roll_call_date' => 'nullable|date',
            'roll_call_start_date' => 'nullable|date|required_if:show_on_roll_call,1',
            'roll_call_end_date' => 'nullable|date|after_or_equal:roll_call_start_date',
        ]);
    }

    private function fillPost(FeedPost $post, Request $request, array $validated): void
    {
        $entertainer = $this->approvedEntertainer();

        if ($entertainer) {
            $post->website_id = $entertainer->website_id;
            $post->author_mode = 'model';
            $post->feed_model_id = $entertainer->feed_model_id;
        } else {
            $post->website_id = $validated['website_id'];
            $post->author_mode = $validated['author_mode'];
            $post->feed_model_id = $validated['author_mode'] === 'model' ? ($validated['feed_model_id'] ?? null) : null;
        }

        $post->caption = $validated['caption'] ?? null;
        $post->posted_at = $validated['posted_at'] ?? now();
        $post->is_active = $request->boolean('is_active', true);
        if ($entertainer) {
            // Verified entertainer posts go live immediately, but still require admin review.
            $post->approval_status = 'approved';
            $post->approved_at = now();
            $post->approved_by = null;
            $post->review_required = true;
            $post->reviewed_at = null;
        } else {
            // Admin-created feed posts (club/managed profile flow) are auto-approved.
            $post->approval_status = 'approved';
            $post->approved_at = now();
            $post->approved_by = auth()->id();
            $post->review_required = false;
            $post->reviewed_at = now();
        }
        $post->show_on_roll_call = $this->canUseRollCall($post, $entertainer)
            ? $request->boolean('show_on_roll_call', false)
            : false;

        $resolvedRollCallStart = $validated['roll_call_start_date']
            ?? $validated['roll_call_date']
            ?? optional($post->posted_at)->format('Y-m-d')
            ?? now()->format('Y-m-d');
        $resolvedRollCallEnd = $validated['roll_call_end_date'] ?? $resolvedRollCallStart;

        $post->roll_call_start_date = $post->show_on_roll_call ? $resolvedRollCallStart : null;
        $post->roll_call_end_date = $post->show_on_roll_call ? $resolvedRollCallEnd : null;
        // Keep legacy single-date column populated as the start date for older queries/views.
        $post->roll_call_date = $post->show_on_roll_call ? $resolvedRollCallStart : null;

        $existingMediaItems = $this->extractExistingMediaItems($post, $request->input('existing_media_keys', []));
        $uploadedMediaItems = $this->extractUploadedMediaItems((array) $request->file('media_uploads', []));
        $externalMediaItems = $this->extractExternalMediaItems(
            (array) $request->input('external_media_links', []),
            (array) $request->input('external_media_types', [])
        );

        $mediaItems = array_values(array_filter(array_merge($existingMediaItems, $uploadedMediaItems, $externalMediaItems), function ($item) {
            return !empty($item['url']);
        }));

        $post->media_items = $mediaItems;
        $post->images = array_values(array_map(function ($item) {
            return $item['type'] === 'image' && $item['source'] === 'upload' ? $item['url'] : null;
        }, $mediaItems));
    }

    private function accessibleWebsites(): Collection
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return Website::orderBy('name')->get();
        }

        if ($user->isWebsiteUser() && $user->website_id) {
            return Website::where('id', $user->website_id)->orderBy('name')->get();
        }

        if ($user->isEntertainer()) {
            $entertainer = $this->approvedEntertainer();
            if ($entertainer) {
                return Website::where('id', $entertainer->website_id)->orderBy('name')->get();
            }
        }

        abort(403);
    }

    private function accessibleWebsiteIds(): array
    {
        return $this->accessibleWebsites()->pluck('id')->all();
    }

    private function accessibleFeedModels(): Collection
    {
        $entertainer = $this->approvedEntertainer();
        if ($entertainer) {
            return FeedModel::with('website')
                ->where('id', $entertainer->feed_model_id)
                ->whereIn('website_id', $this->accessibleWebsiteIds())
                ->orderBy('name')
                ->get();
        }

        return FeedModel::with('website')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->where('is_real_profile', false)
            ->orderBy('name')
            ->get();
    }

    private function ensureWebsiteAccess(int $websiteId): void
    {
        if (!in_array($websiteId, $this->accessibleWebsiteIds(), true)) {
            abort(403, 'Access denied for this website.');
        }
    }

    private function ensureFeedModelAccess(int $feedModelId, int $websiteId): void
    {
        $exists = FeedModel::where('id', $feedModelId)
            ->where('website_id', $websiteId)
            ->where('is_real_profile', false)
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->exists();

        if (!$exists) {
            abort(403, 'Selected entertainer does not belong to the chosen website.');
        }
    }

    private function ensurePostManagementAccess(FeedPost $feedPost): void
    {
        $this->ensureWebsiteAccess($feedPost->website_id);

        $entertainer = $this->approvedEntertainer();
        if (!$entertainer) {
            return;
        }

        if ((string) $feedPost->author_mode !== 'model' || (int) $feedPost->feed_model_id !== (int) $entertainer->feed_model_id) {
            abort(403, 'You can only manage your own feed posts.');
        }
    }

    private function ensureApprovePermission(FeedPost $feedPost): void
    {
        $this->ensureWebsiteAccess($feedPost->website_id);

        $user = auth()->user();
        if (!$user || (!$user->isAdmin() && !$user->isWebsiteUser())) {
            abort(403, 'Only super admin or club admin can approve entertainer posts.');
        }
    }

    private function ensureAuthorSelection(array $validated): void
    {
        $entertainer = $this->approvedEntertainer();
        if ($entertainer) {
            if (($validated['author_mode'] ?? 'model') !== 'model') {
                abort(422, 'Entertainers can only post as their own entertainer profile.');
            }

            if ((int) ($validated['website_id'] ?? 0) !== (int) $entertainer->website_id) {
                abort(403, 'You can only post to your assigned club.');
            }

            if ((int) ($validated['feed_model_id'] ?? 0) !== (int) $entertainer->feed_model_id) {
                abort(403, 'You can only post using your own entertainer profile.');
            }

            return;
        }

        if (($validated['author_mode'] ?? 'model') === 'club') {
            return;
        }

        if (empty($validated['feed_model_id'])) {
            abort(422, 'Please select an entertainer profile when posting as an entertainer.');
        }

        $this->ensureFeedModelAccess((int) $validated['feed_model_id'], (int) $validated['website_id']);
    }

    private function extractExistingMediaItems(FeedPost $post, array $keys): array
    {
        $resolved = array_values($post->resolved_media_items ?? []);
        $keys = array_map('strval', $keys);

        return array_values(array_filter($resolved, function ($item, $index) use ($keys) {
            return in_array((string) $index, $keys, true);
        }, ARRAY_FILTER_USE_BOTH));
    }

    private function extractUploadedMediaItems(array $files): array
    {
        $items = [];

        foreach ($files as $index => $file) {
            if (!$file) {
                continue;
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $isVideo = in_array($extension, ['mp4', 'mov', 'webm', 'ogg'], true);
            $items[] = [
                'type' => $isVideo ? 'video' : 'image',
                'source' => 'upload',
                'url' => $this->storeMediaFile($file, 'feed_post_' . $index),
            ];
        }

        return $items;
    }

    private function extractExternalMediaItems(array $links, array $types): array
    {
        $items = [];

        foreach ($links as $index => $link) {
            $url = trim((string) $link);
            if ($url === '') {
                continue;
            }

            $items[] = [
                'type' => $types[$index] ?? $this->guessMediaType($url),
                'source' => 'external',
                'url' => $url,
            ];
        }

        return $items;
    }

    private function guessMediaType(string $url): string
    {
        return preg_match('/\.(mp4|mov|webm|ogg)(\?.*)?$/i', $url) ? 'video' : 'image';
    }

    private function storeMediaFile($file, string $prefix): string
    {
        $fileName = $prefix . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $fileName);

        return $fileName;
    }

    private function storeImage($file, string $prefix): string
    {
        return $this->storeMediaFile($file, $prefix);
    }

    private function approvedEntertainer()
    {
        $user = auth()->user();

        if (!$user || !$user->isEntertainer()) {
            return null;
        }

        $entertainer = $user->entertainer;
        if (!$entertainer || $entertainer->status !== 'approved' || !$entertainer->is_active || !$entertainer->feed_model_id) {
            abort(403, 'Entertainer posting access denied.');
        }

        return $entertainer;
    }

    private function canUseRollCall(FeedPost $post, $entertainer): bool
    {
        if ($entertainer) {
            return false;
        }

        if ((string) $post->author_mode !== 'model' || empty($post->feed_model_id)) {
            return false;
        }

        $feedModel = FeedModel::find($post->feed_model_id);

        return $feedModel ? !$feedModel->is_real_profile : false;
    }
}
