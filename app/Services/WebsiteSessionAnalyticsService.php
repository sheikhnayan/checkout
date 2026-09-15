<?php

namespace App\Services;

use App\Models\Website;
use App\Models\WebsiteVisitorSession;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WebsiteSessionAnalyticsService
{
    public function trackCheckoutPageView(Request $request, Website $website, ?int $affiliateId = null, ?int $entertainerId = null): void
    {
        if (!Schema::hasTable('website_visitor_sessions')) {
            return;
        }

        if (strtoupper((string) $request->method()) !== 'GET') {
            return;
        }

        if ($request->ajax()) {
            return;
        }

        if (!$request->hasSession()) {
            return;
        }

        $userAgent = trim((string) ($request->userAgent() ?? ''));
        if ($this->isBotUserAgent($userAgent)) {
            return;
        }

        $sessionId = $this->resolveAnalyticsSessionId($request, $website->id);
        if ($sessionId === '') {
            return;
        }

        // Auto-resolve promoter / affiliate referral if not explicitly passed
        if ($affiliateId === null) {
            if ($request->filled('aff')) {
                $affSlug = trim((string) $request->query('aff'));
                $aff = \App\Models\Affiliate::where('slug', $affSlug)
                    ->where('status', 'approved')
                    ->where('is_active', true)
                    ->first();
                if ($aff) {
                    $affiliateId = $aff->id;
                }
            } elseif ($request->session()->has('affiliate_referral_id')) {
                $affiliateId = (int) $request->session()->get('affiliate_referral_id');
            }
        }

        // Auto-resolve entertainer referral if not explicitly passed
        if ($entertainerId === null) {
            if ($request->filled('ent') || $request->filled('entertainer')) {
                $entSlug = trim((string) ($request->query('ent') ?: $request->query('entertainer')));
                $ent = \App\Models\Entertainer::where('slug', $entSlug)
                    ->where('status', 'approved')
                    ->where('is_active', true)
                    ->first();
                if ($ent) {
                    $entertainerId = $ent->id;
                }
            } elseif ($request->session()->has('entertainer_referral_id')) {
                $entertainerId = (int) $request->session()->get('entertainer_referral_id');
            }
        }

        $channelSource = 'direct';
        if ($affiliateId) {
            $channelSource = 'promoter';
        } elseif ($entertainerId) {
            $channelSource = 'entertainer';
        }

        $now = now();
        $ip = trim((string) ($request->ip() ?? ''));
        $visitorKey = hash('sha256', strtolower($ip) . '|' . strtolower($userAgent));
        $path = '/' . ltrim($request->path(), '/');

        $referrerHost = null;
        $referrer = trim((string) $request->headers->get('referer', ''));
        if ($referrer !== '') {
            $parsedHost = parse_url($referrer, PHP_URL_HOST);
            if (is_string($parsedHost)) {
                $referrerHost = trim(strtolower($parsedHost));
            }
        }

        $hasAffiliateCol = Schema::hasColumn('website_visitor_sessions', 'affiliate_id');
        $hasEntertainerCol = Schema::hasColumn('website_visitor_sessions', 'entertainer_id');
        $hasChannelCol = Schema::hasColumn('website_visitor_sessions', 'channel_source');

        $trackData = [
            'visitor_key' => $visitorKey,
            'ip_address' => $ip !== '' ? $ip : null,
            'user_agent' => $userAgent !== '' ? mb_substr($userAgent, 0, 1024) : null,
            'referrer_host' => $referrerHost !== '' ? $referrerHost : null,
            'utm_source' => $this->nullIfEmpty($request->query('utm_source')),
            'utm_medium' => $this->nullIfEmpty($request->query('utm_medium')),
            'utm_campaign' => $this->nullIfEmpty($request->query('utm_campaign')),
            'utm_term' => $this->nullIfEmpty($request->query('utm_term')),
            'utm_content' => $this->nullIfEmpty($request->query('utm_content')),
            'last_seen_at' => $now,
        ];

        if ($hasAffiliateCol) {
            $trackData['affiliate_id'] = $affiliateId;
        }
        if ($hasEntertainerCol) {
            $trackData['entertainer_id'] = $entertainerId;
        }
        if ($hasChannelCol) {
            $trackData['channel_source'] = $channelSource;
        }

        $session = WebsiteVisitorSession::where('website_id', $website->id)
            ->where('session_id', $sessionId)
            ->first();

        if (!$session) {
            try {
                WebsiteVisitorSession::create(array_merge($trackData, [
                    'website_id' => $website->id,
                    'session_id' => $sessionId,
                    'landing_path' => $path,
                    'first_seen_at' => $now,
                    'page_views' => 1,
                ]));
                return;
            } catch (QueryException $exception) {
                // Handle race condition on unique key and continue with update path.
                $session = WebsiteVisitorSession::where('website_id', $website->id)
                    ->where('session_id', $sessionId)
                    ->first();
            }
        }

        if (!$session) {
            return;
        }

        $session->last_seen_at = $now;
        $session->page_views = max(1, (int) $session->page_views) + 1;

        if (empty($session->landing_path)) {
            $session->landing_path = $path;
        }

        // Attribute promoter if session previously was direct and now visited via promoter
        if ($hasAffiliateCol && $affiliateId && empty($session->affiliate_id)) {
            $session->affiliate_id = $affiliateId;
            if ($hasChannelCol) {
                $session->channel_source = 'promoter';
            }
        }
        if ($hasEntertainerCol && $entertainerId && empty($session->entertainer_id)) {
            $session->entertainer_id = $entertainerId;
            if ($hasChannelCol && empty($session->affiliate_id)) {
                $session->channel_source = 'entertainer';
            }
        }

        foreach (['visitor_key', 'ip_address', 'user_agent', 'referrer_host', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $field) {
            if (empty($session->{$field}) && !empty($trackData[$field])) {
                $session->{$field} = $trackData[$field];
            }
        }

        $session->save();
    }

    public function trackAffiliatePublicPageView(Request $request, \App\Models\Affiliate $affiliate, ?Website $website = null): void
    {
        if (!$website) {
            $website = $affiliate->affiliateWebsites()
                ->with('website')
                ->where('is_active', true)
                ->first()
                ?->website;
        }

        if ($website) {
            $this->trackCheckoutPageView($request, $website, $affiliate->id);
        }
    }

    private function resolveAnalyticsSessionId(Request $request, int $websiteId): string
    {
        $session = $request->session();
        $baseSessionId = trim((string) $session->getId());
        if ($baseSessionId === '') {
            return '';
        }

        $stateKey = 'analytics.website_session.' . $websiteId;
        $state = $session->get($stateKey, []);
        $state = is_array($state) ? $state : [];

        $logicalId = trim((string) ($state['id'] ?? ''));
        $lastSeenRaw = trim((string) ($state['last_seen_at'] ?? ''));
        $now = now();

        $lastSeenAt = null;
        if ($lastSeenRaw !== '') {
            try {
                $lastSeenAt = Carbon::parse($lastSeenRaw);
            } catch (\Throwable $exception) {
                $lastSeenAt = null;
            }
        }

        $shouldRotate = !$lastSeenAt || $lastSeenAt->lt($now->copy()->subMinutes(30));
        if ($logicalId === '' || $shouldRotate) {
            $logicalId = $baseSessionId . '-' . Str::uuid()->toString();
        }

        $session->put($stateKey, [
            'id' => $logicalId,
            'last_seen_at' => $now->toIso8601String(),
        ]);

        return $logicalId;
    }

    private function nullIfEmpty($value): ?string
    {
        $text = trim((string) ($value ?? ''));
        return $text === '' ? null : mb_substr($text, 0, 255);
    }

    private function isBotUserAgent(string $userAgent): bool
    {
        if ($userAgent === '') {
            return false;
        }

        $needle = strtolower($userAgent);
        return str_contains($needle, 'bot')
            || str_contains($needle, 'spider')
            || str_contains($needle, 'crawl')
            || str_contains($needle, 'preview')
            || str_contains($needle, 'slurp');
    }
}
