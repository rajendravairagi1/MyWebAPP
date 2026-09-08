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

    public function index()
    {
        $logoPath = SiteSetting::get('logo_path');

        return view('admin.branding.index', compact('logoPath'));
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
