<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        return view('admin.theme.index', [
            'activeTheme' => SiteSetting::theme(),
            'activeAdminTheme' => SiteSetting::adminTheme(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:'.implode(',', SiteSetting::THEMES),
            'admin_theme' => 'required|in:'.implode(',', SiteSetting::THEMES),
        ]);

        SiteSetting::setTheme($validated['theme']);
        SiteSetting::setAdminTheme($validated['admin_theme']);

        return redirect()->route('admin.theme.index')->with('status', 'Theme updated.');
    }
}
