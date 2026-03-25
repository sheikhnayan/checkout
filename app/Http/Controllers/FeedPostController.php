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
    public function index(): View
    {
        $websiteIds = $this->accessibleWebsiteIds();

        $posts = FeedPost::with(['website', 'feedModel'])
            ->withCount('comments')
            ->whereIn('website_id', $websiteIds)
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
        $this->ensureWebsiteAccess($feedPost->website_id);
        $feedPost->load(['website', 'feedModel', 'comments']);

        return view('admin.feed-post.show', [
            'feedPost' => $feedPost,
        ]);
    }

    public function edit(FeedPost $feedPost): View
    {
        $this->ensureWebsiteAccess($feedPost->website_id);

        return view('admin.feed-post.edit', [
            'feedPost' => $feedPost,
            'websites' => $this->accessibleWebsites(),
            'feedModels' => $this->accessibleFeedModels(),
        ]);
    }

    public function update(Request $request, FeedPost $feedPost): RedirectResponse
    {
        $validated = $this->validatePost($request);
        $this->ensureWebsiteAccess($feedPost->website_id);
        $this->ensureWebsiteAccess((int) $validated['website_id']);
        $this->ensureAuthorSelection($validated);

        $this->fillPost($feedPost, $request, $validated);
        $feedPost->save();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post updated successfully.');
    }

    public function destroy(FeedPost $feedPost): RedirectResponse
    {
        $this->ensureWebsiteAccess($feedPost->website_id);
        $feedPost->delete();

        return redirect()->route('admin.feed-post.index')->with('success', 'Feed post deleted successfully.');
    }

    public function toggleCommentVisibility(FeedPost $feedPost, FeedComment $feedComment): RedirectResponse
    {
        $this->ensureWebsiteAccess($feedPost->website_id);

        if ($feedComment->feed_post_id !== $feedPost->id) {
            abort(404);
        }

        $feedComment->is_visible = !$feedComment->is_visible;
        $feedComment->save();

        return redirect()->route('admin.feed-post.show', $feedPost)->with('success', 'Comment visibility updated.');
    }

    public function destroyComment(FeedPost $feedPost, FeedComment $feedComment): RedirectResponse
    {
        $this->ensureWebsiteAccess($feedPost->website_id);

        if ($feedComment->feed_post_id !== $feedPost->id) {
            abort(404);
        }

        $feedComment->delete();

        return redirect()->route('admin.feed-post.show', $feedPost)->with('success', 'Comment deleted successfully.');
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'website_id' => 'required|exists:websites,id',
            'author_mode' => 'required|in:model,club',
            'feed_model_id' => 'nullable|exists:feed_models,id',
            'caption' => 'nullable|string|max:10000',
            'media_uploads' => 'nullable|array|max:12',
            'media_uploads.*' => 'nullable|file|mimes:jpg,jpeg,png,webp,gif,mp4,mov,webm,ogg|max:20480',
            'external_media_links' => 'nullable|array|max:20',
            'external_media_links.*' => 'nullable|url|max:2000',
            'external_media_types' => 'nullable|array|max:20',
            'external_media_types.*' => 'nullable|in:image,video',
            'posted_at' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);
    }

    private function fillPost(FeedPost $post, Request $request, array $validated): void
    {
        $post->website_id = $validated['website_id'];
        $post->author_mode = $validated['author_mode'];
        $post->feed_model_id = $validated['author_mode'] === 'model' ? ($validated['feed_model_id'] ?? null) : null;
        $post->caption = $validated['caption'] ?? null;
        $post->posted_at = $validated['posted_at'] ?? now();
        $post->is_active = $request->boolean('is_active', true);

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

        abort(403);
    }

    private function accessibleWebsiteIds(): array
    {
        return $this->accessibleWebsites()->pluck('id')->all();
    }

    private function accessibleFeedModels(): Collection
    {
        return FeedModel::with('website')
            ->whereIn('website_id', $this->accessibleWebsiteIds())
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
            ->whereIn('website_id', $this->accessibleWebsiteIds())
            ->exists();

        if (!$exists) {
            abort(403, 'Selected model does not belong to the chosen website.');
        }
    }

    private function ensureAuthorSelection(array $validated): void
    {
        if (($validated['author_mode'] ?? 'model') === 'club') {
            return;
        }

        if (empty($validated['feed_model_id'])) {
            abort(422, 'Please select a model when posting as a model.');
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
}
