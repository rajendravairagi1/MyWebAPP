<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * Third-party integration keys that shouldn't need a code deploy to
 * change — currently just Google reCAPTCHA v2 for the Contact form.
 * Stored via SiteSetting (same key/value table as the theme toggle),
 * not .env, so the site owner can set/rotate them from /admin without
 * needing file access.
 */
class IntegrationsController extends Controller
{
    public function index()
    {
        return view('admin.integrations.index', [
            'recaptchaSiteKey' => SiteSetting::get('recaptcha_site_key'),
            'recaptchaSecretKey' => SiteSetting::get('recaptcha_secret_key'),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
        ]);

        SiteSetting::set('recaptcha_site_key', trim($validated['recaptcha_site_key'] ?? ''));
        SiteSetting::set('recaptcha_secret_key', trim($validated['recaptcha_secret_key'] ?? ''));

        return redirect()->route('admin.integrations.index')->with('status', 'Integrations updated.');
    }
}
