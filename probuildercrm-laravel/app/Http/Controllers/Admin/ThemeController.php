<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    public function index()
    {
        $activeTheme = SiteSetting::theme();

        return view('admin.theme.index', compact('activeTheme'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => 'required|in:'.implode(',', SiteSetting::THEMES),
        ]);

        SiteSetting::setTheme($validated['theme']);

        return redirect()->route('admin.theme.index')->with('status', 'Theme updated.');
    }
}
