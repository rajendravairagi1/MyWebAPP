<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
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
        $file->move($dir, $filename);

        SiteSetting::set('logo_path', self::DIR.'/'.$filename);

        return redirect()->route('admin.branding.index')->with('status', 'Logo updated.');
    }

    public static function pixelsFor(?string $size): int
    {
        return self::SIZES[$size] ?? self::SIZES[self::DEFAULT_SIZE];
    }

    public function destroy()
    {
        $this->deleteExistingLogo();
        SiteSetting::set('logo_path', '');

        return redirect()->route('admin.branding.index')->with('status', 'Logo removed — back to the text logo.');
    }

    private function deleteExistingLogo(): void
    {
        foreach (glob(public_path(self::DIR.'/logo.*')) ?: [] as $existing) {
            @unlink($existing);
        }
    }
}
