<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

/**
 * The site logo shown in the navbar/footer. Stored as a plain file under
 * public/branding/ (not the storage/ disk + `storage:link` symlink) so it
 * works the moment a zip is extracted on shared hosting, with no extra
 * `php artisan storage:link` step to remember or get wrong.
 */
class BrandingController extends Controller
{
    private const DIR = 'branding';

    /**
     * Preset logo heights (px) offered in the admin dropdown — labelled
     * so a non-technical owner can just try each until one looks right,
     * rather than needing to know pixel values themselves.
     */
    public const SIZES = [
        'xs' => 24,
        'sm' => 32,
        'md' => 44,
        'lg' => 60,
        'xl' => 80,
    ];

    public const DEFAULT_SIZE = 'md';

    public function index()
    {
        $logoPath = SiteSetting::get('logo_path');
        $logoSize = SiteSetting::get('logo_size', self::DEFAULT_SIZE);

        return view('admin.branding.index', [
            'logoPath' => $logoPath,
            'logoSize' => $logoSize,
            'sizes' => self::SIZES,
            'favicons' => self::faviconLinks(),
            'hasCustomFavicon' => (bool) SiteSetting::get('custom_favicon'),
        ]);
    }

    public function updateSize(Request $request)
    {
        $request->validate([
            'logo_size' => ['required', 'in:'.implode(',', array_keys(self::SIZES))],
        ]);

        SiteSetting::set('logo_size', $request->input('logo_size'));

        return redirect()->route('admin.branding.index')->with('status', 'Logo size updated.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $dir = public_path(self::DIR);
        File::ensureDirectoryExists($dir);

        // Remove any previously uploaded logo first — old extension may
        // differ from the new one, and only one logo file should exist.
        $this->deleteExistingLogo();

        $file = $request->file('logo');
        $filename = 'logo.'.$file->getClientOriginalExtension();
        ImageOptimizer::optimizeAndSave($file, $dir.'/'.$filename, maxWidth: 800, quality: 90);

        SiteSetting::set('logo_path', self::DIR.'/'.$filename);

        return redirect()->route('admin.branding.index')->with('status', 'Logo updated.');
    }

    public static function pixelsFor(?string $size): int
    {
        return self::SIZES[$size] ?? self::SIZES[self::DEFAULT_SIZE];
    }

    /**
     * A stable, extension-agnostic logo URL other apps (businessflow's
     * app.probuildercrm.com) can hardcode permanently - the uploaded
     * file's actual extension can change on every re-upload, but this
     * URL never does. Redirects to whatever the current file is, or 404s
     * if no logo is uploaded so the caller's own fallback can kick in.
     */
    public function showLogo()
    {
        $logoPath = SiteSetting::get('logo_path');

        abort_unless($logoPath, 404);

        return redirect(asset($logoPath));
    }

    public function destroy()
    {
        $this->deleteExistingLogo();
        SiteSetting::set('logo_path', '');

        return redirect()->route('admin.branding.index')->with('status', 'Logo removed - back to the text logo.');
    }

    private function deleteExistingLogo(): void
    {
        foreach (glob(public_path(self::DIR.'/logo.*')) ?: [] as $existing) {
            @unlink($existing);
        }
    }

    /**
     * The served favicon files always live at the public/ root
     * (favicon.ico, favicon-32.png, ...) — not just for <link> tags, but
     * because browsers request /favicon.ico directly (their built-in
     * fallback) for ANY page, including plain-text/XML responses like
     * /sitemap.xml and /robots.txt that have no <head> to put a <link> in.
     * A true backup of the bundled defaults lives at
     * public/favicon-defaults/, untouched by uploads, so "reset to
     * default" has something to restore from.
     */
    private const FAVICON_FILES = ['favicon.ico', 'favicon-32.png', 'favicon-16.png', 'apple-touch-icon.png', 'favicon.svg'];

    private const FAVICON_DEFAULTS_DIR = 'favicon-defaults';

    public function updateFavicon(Request $request)
    {
        $request->validate([
            'favicon' => ['required', 'file', 'max:1024', function ($attribute, $value, $fail) {
                if (! in_array(strtolower($value->getClientOriginalExtension()), ['png', 'jpg', 'jpeg', 'webp', 'svg'])) {
                    $fail('The favicon must be a PNG, JPG, WEBP or SVG file.');
                }
            }],
        ]);

        $this->generateFavicons($request->file('favicon'));
        SiteSetting::set('custom_favicon', '1');

        return redirect()->route('admin.branding.index')->with('status', 'Favicon updated.');
    }

    public function destroyFavicon()
    {
        $defaultsDir = public_path(self::FAVICON_DEFAULTS_DIR);
        foreach (self::FAVICON_FILES as $filename) {
            $default = $defaultsDir.'/'.$filename;
            if (file_exists($default)) {
                copy($default, public_path($filename));
            } else {
                @unlink(public_path($filename));
            }
        }
        SiteSetting::set('custom_favicon', '');

        return redirect()->route('admin.branding.index')->with('status', 'Favicon reset to the default.');
    }

    /**
     * Returns the <link>-ready URLs for the currently active favicon set —
     * used by both the marketing and admin layouts and by the branding
     * admin page's preview. Always the public/ root, since that's what's
     * actually served now (see the FAVICON_FILES doc comment above).
     */
    public static function faviconLinks(): array
    {
        return [
            'ico' => asset('favicon.ico'),
            'png32' => asset('favicon-32.png'),
            'png16' => asset('favicon-16.png'),
            'apple' => asset('apple-touch-icon.png'),
            'svg' => file_exists(public_path('favicon.svg')) ? asset('favicon.svg') : null,
        ];
    }

    private function generateFavicons(UploadedFile $file): void
    {
        $dir = public_path();

        foreach (self::FAVICON_FILES as $filename) {
            @unlink($dir.'/'.$filename);
        }

        $ext = strtolower($file->getClientOriginalExtension());

        if ($ext === 'svg') {
            copy($file->getRealPath(), $dir.'/favicon.svg');

            return;
        }

        if (! extension_loaded('gd')) {
            return;
        }

        $source = match ($ext) {
            'png' => @imagecreatefrompng($file->getRealPath()),
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $source) {
            return;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);
        $srcX = (int) (($width - $side) / 2);
        $srcY = (int) (($height - $side) / 2);

        foreach ([32 => 'favicon-32.png', 16 => 'favicon-16.png', 180 => 'apple-touch-icon.png'] as $size => $filename) {
            $out = imagecreatetruecolor($size, $size);
            imagealphablending($out, false);
            imagesavealpha($out, true);
            $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
            imagefill($out, 0, 0, $transparent);
            imagealphablending($out, true);
            imagecopyresampled($out, $source, 0, 0, $srcX, $srcY, $size, $size, $side, $side);
            imagepng($out, $dir.'/'.$filename);
            imagedestroy($out);
        }
        imagedestroy($source);

        // Wrap the 32px PNG in a minimal ICO container (valid since
        // Vista+ / all modern browsers) so plain /favicon.ico requests
        // — which some browsers make regardless of <link> tags — work.
        $png = file_get_contents($dir.'/favicon-32.png');
        $ico = pack('vvv', 0, 1, 1).pack('CCCCvvVV', 32, 32, 0, 0, 1, 32, strlen($png), 22);
        file_put_contents($dir.'/favicon.ico', $ico.$png);
    }
}
