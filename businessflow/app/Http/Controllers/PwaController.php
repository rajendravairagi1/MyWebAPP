<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Support\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Powers "Add to Home Screen" — the manifest and the icon it points at
 * are both generated per request rather than static files, so whichever
 * logo a business has uploaded for its branding (Business Settings) is
 * exactly what shows up as its mobile home-screen icon too. Falls back
 * to the shared brand favicon (see config('app.brand_favicon_apple_url'))
 * for a business with no logo uploaded yet, or when no business is
 * active at all (not logged in, or mid-onboarding) — e.g. installing
 * straight from the login page — and only drops to a plain
 * initial-letter icon if even that favicon fetch fails.
 */
class PwaController extends Controller
{
    public function manifest(): JsonResponse
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;
        $name = $business?->name ?: config('app.name', 'Pro Builder CRM');

        $manifest = [
            'name' => $name,
            'short_name' => \Illuminate\Support\Str::limit($name, 12, ''),
            'start_url' => url('/dashboard'),
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#4f46e5',
            'icons' => [
                ['src' => url('/pwa-icon/192'), 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any maskable'],
                ['src' => url('/pwa-icon/512'), 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
            ],
        ];

        return response()->json($manifest)->header('Content-Type', 'application/manifest+json');
    }

    public function icon(Request $request, int $size): Response
    {
        $size = in_array($size, [192, 512], true) ? $size : 192;

        $business = Tenant::check() ? Business::find(Tenant::id()) : null;
        $png = $this->renderIcon($business, $size);

        // A generic time-based cache would keep serving a stale icon for
        // up to an hour after a business changes its logo — hashing the
        // rendered bytes into an ETag instead means the browser always
        // gets the current icon the moment it actually changes, while
        // still skipping re-download when it hasn't.
        return response($png, 200)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'private, no-cache')
            ->setEtag(md5($png));
    }

    private function renderIcon(?Business $business, int $size): string
    {
        try {
            if ($business?->logo_path && Storage::disk('local')->exists($business->logo_path)) {
                return $this->renderFromLogo($business, $size);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // Not logged into a business yet (e.g. installing straight from
        // the login page) — use the shared brand favicon instead of a
        // generic initial-letter icon, so "Install App" puts the same
        // icon on the home screen that's already in the browser tab.
        try {
            return $this->renderFromFavicon($size);
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->renderInitial($business, $size);
    }

    /**
     * Fetches the marketing site's apple-touch-icon (a 180x180 PNG
     * generated from whatever favicon is uploaded in Admin > Branding —
     * see BrandingController::generateFavicons()) and fits it onto a
     * square canvas the same way a business logo would be. Cached for
     * 10 minutes so this never adds a live cross-app request to every
     * icon load, and a short timeout means a slow/down marketing site
     * just falls through to the initial-letter icon instead of hanging.
     */
    private function renderFromFavicon(int $size): string
    {
        $bytes = Cache::remember('brand_favicon_icon_bytes', now()->addMinutes(10), function () {
            $response = Http::timeout(2)->get(config('app.brand_favicon_apple_url'));

            return $response->ok() ? $response->body() : null;
        });

        if (! $bytes) {
            throw new \RuntimeException('Brand favicon unavailable.');
        }

        $source = @imagecreatefromstring($bytes);

        if (! $source) {
            throw new \RuntimeException('Brand favicon could not be decoded.');
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);
        $scale = min($size / $srcW, $size / $srcH);
        $dstW = max(1, (int) round($srcW * $scale));
        $dstH = max(1, (int) round($srcH * $scale));
        $offsetX = (int) (($size - $dstW) / 2);
        $offsetY = (int) (($size - $dstH) / 2);

        $canvas = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        imagecopyresampled($canvas, $source, $offsetX, $offsetY, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($source);

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }

    /**
     * Fits the uploaded logo onto a square white canvas (contain, not
     * crop) so the whole thing stays visible regardless of its original
     * aspect ratio — a wide logo doesn't get its edges cut off.
     */
    private function renderFromLogo(Business $business, int $size): string
    {
        $bytes = Storage::disk('local')->get($business->logo_path);
        $source = @imagecreatefromstring($bytes);

        if (! $source) {
            return $this->renderInitial($business, $size);
        }

        $srcW = imagesx($source);
        $srcH = imagesy($source);
        $scale = min($size / $srcW, $size / $srcH);
        $dstW = max(1, (int) round($srcW * $scale));
        $dstH = max(1, (int) round($srcH * $scale));
        $offsetX = (int) (($size - $dstW) / 2);
        $offsetY = (int) (($size - $dstH) / 2);

        $canvas = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $white);
        imagealphablending($canvas, true);
        imagesavealpha($canvas, true);

        imagecopyresampled($canvas, $source, $offsetX, $offsetY, 0, 0, $dstW, $dstH, $srcW, $srcH);
        imagedestroy($source);

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }

    private function renderInitial(?Business $business, int $size): string
    {
        $letter = strtoupper(substr($business?->name ?: 'B', 0, 1));

        $canvas = imagecreatetruecolor($size, $size);
        $indigo = imagecolorallocate($canvas, 79, 70, 229); // matches the app's default accent-600
        imagefill($canvas, 0, 0, $indigo);

        $font = 5; // GD's largest built-in bitmap font
        $textW = imagefontwidth($font) * strlen($letter);
        $textH = imagefontheight($font);
        $scale = (int) max(1, round($size / 96));

        $tmp = imagecreatetruecolor($textW, $textH);
        imagefill($tmp, 0, 0, imagecolorallocate($tmp, 79, 70, 229));
        imagestring($tmp, $font, 0, 0, $letter, imagecolorallocate($tmp, 255, 255, 255));

        imagecopyresized(
            $canvas, $tmp,
            (int) (($size - $textW * $scale) / 2), (int) (($size - $textH * $scale) / 2),
            0, 0,
            $textW * $scale, $textH * $scale,
            $textW, $textH
        );
        imagedestroy($tmp);

        ob_start();
        imagepng($canvas);
        $png = ob_get_clean();
        imagedestroy($canvas);

        return $png;
    }
}
