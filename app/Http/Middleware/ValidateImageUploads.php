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

        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
            'image/svg+xml',
        ];

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
        $maxBytes = 4 * 1024 * 1024;

        foreach ($this->flattenFiles($files) as $file) {
            if (!$file || !$file->isValid()) {
                return $this->rejectUpload($request, 'Image upload failed. Please select a valid image and try again.');
            }

            $mime = strtolower((string) $file->getMimeType());
            $ext = strtolower((string) $file->getClientOriginalExtension());

            if (!in_array($mime, $allowedMimeTypes, true) || !in_array($ext, $allowedExtensions, true)) {
                return $this->rejectUpload(
                    $request,
                    'Only JPG, PNG, WEBP, GIF, and SVG image files are allowed.'
                );
            }

            if ($file->getSize() > $maxBytes) {
                return $this->rejectUpload($request, 'Image file is too large. Maximum allowed size is 4MB per image.');
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
}
