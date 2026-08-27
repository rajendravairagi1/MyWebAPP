<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Support\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

/**
 * Powers "Add to Home Screen" — the manifest and the icon it points at
 * are both generated per request rather than static files, so whichever
 * logo a business has uploaded for its branding (Business Settings) is
 * exactly what shows up as its mobile home-screen icon too. Falls back
 * to a plain initial-letter icon for a business with no logo uploaded
 * yet, and to the app's own name/icon when no business is active at all
 * (not logged in, or mid-onboarding).
 */
class PwaController extends Controller
{
    public function manifest(): JsonResponse
    {
        $business = Tenant::check() ? Business::find(Tenant::id()) : null;
        $name = $business?->name ?: config('app.name', 'BusinessFlow');

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

        return $this->renderInitial($business, $size);
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
