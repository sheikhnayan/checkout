<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateImageUploads
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $files = $request->allFiles();

        if (empty($files)) {
            return $next($request);
        }

        $profile = $this->uploadProfile($request);

        foreach ($this->flattenFiles($files) as $file) {
            if (!$file || !$file->isValid()) {
                $invalidReason = $file ? $file->getErrorMessage() : 'Unknown upload error.';
                return $this->rejectUpload($request, $profile['invalid_message'] . ' Details: ' . $invalidReason);
            }

            $mime = strtolower((string) $file->getMimeType());
            $ext = strtolower((string) $file->getClientOriginalExtension());
            $name = (string) $file->getClientOriginalName();
            $size = (int) $file->getSize();
            $allowedExt = strtoupper(implode(', ', $profile['allowed_extensions']));
            $maxMb = (int) round($profile['max_bytes'] / (1024 * 1024));

            if (!in_array($mime, $profile['allowed_mime_types'], true) || !in_array($ext, $profile['allowed_extensions'], true)) {
                return $this->rejectUpload(
                    $request,
                    $profile['type_message']
                    . " File: {$name}. Detected extension: {$ext}. MIME type: {$mime}. Allowed extensions: {$allowedExt}."
                );
            }

            if ($file->getSize() > $profile['max_bytes']) {
                return $this->rejectUpload(
                    $request,
                    $profile['size_message']
                    . ' File: ' . $name
                    . '. Detected size: ' . $this->formatBytes($size)
                    . '. Max allowed: ' . $maxMb . 'MB.'
                );
            }
        }

        return $next($request);
    }

    private function flattenFiles(array $files): array
    {
        $flat = [];

        array_walk_recursive($files, function ($value) use (&$flat) {
            if (is_object($value)) {
                $flat[] = $value;
            }
        });

        return $flat;
    }

    private function rejectUpload(Request $request, string $message): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'errors' => [
                    'image' => [$message],
                ],
            ], 422);
        }

        return redirect()->back()
            ->withInput()
            ->with('warning', $message)
            ->withErrors(['image' => $message]);
    }

    private function uploadProfile(Request $request): array
    {
        if ($request->routeIs('admin.feed-post.store', 'admin.feed-post.update')) {
            return [
                'allowed_mime_types' => [
                    'image/jpeg',
                    'image/png',
                    'image/webp',
                    'image/gif',
                    'video/mp4',
                    'video/quicktime',
                    'video/webm',
                    'video/ogg',
                    'application/ogg',
                ],
                'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'mp4', 'mov', 'webm', 'ogg'],
                'max_bytes' => 4 * 1024 * 1024,
                'invalid_message' => 'Media upload failed. Please select a valid image or video and try again.',
                'type_message' => 'Only JPG, PNG, WEBP, GIF, MP4, MOV, WEBM, and OGG files are allowed for feed posts.',
                'size_message' => 'Media file is too large. Maximum allowed size is 4MB per file.',
            ];
        }

        return [
            'allowed_mime_types' => [
                'image/jpeg',
                'image/png',
                'image/webp',
                'image/gif',
                'image/svg+xml',
            ],
            'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'],
            'max_bytes' => 4 * 1024 * 1024,
            'invalid_message' => 'Image upload failed. Please select a valid image and try again.',
            'type_message' => 'Only JPG, PNG, WEBP, GIF, and SVG image files are allowed.',
            'size_message' => 'Image file is too large. Maximum allowed size is 4MB per image.',
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return round($bytes / (1024 * 1024), 2) . ' MB';
    }
}
