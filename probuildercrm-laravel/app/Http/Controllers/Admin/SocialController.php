<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

/**
 * Social media links + the header/footer phone number — all optional,
 * all admin-editable, none of it needing a code deploy to change.
 */
class SocialController extends Controller
{
    public const PLATFORMS = ['facebook', 'instagram', 'linkedin', 'twitter', 'whatsapp'];

    public const DEFAULT_FOOTER_DESCRIPTION = 'Pro Builder CRM brings every project, unit and customer payment into one place - built for real estate builders and developers, without spreadsheets or scattered WhatsApp chats.';

    public function index()
    {
        $links = [];
        foreach (self::PLATFORMS as $platform) {
            $links[$platform] = SiteSetting::get('social_'.$platform);
        }

        return view('admin.social.index', [
            'links' => $links,
            'showHeader' => (bool) SiteSetting::get('social_show_header'),
            'showFooter' => (bool) SiteSetting::get('social_show_footer'),
            'phoneNumber' => SiteSetting::get('phone_number'),
            'footerDescription' => SiteSetting::get('footer_description', self::DEFAULT_FOOTER_DESCRIPTION),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'social_facebook' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_whatsapp' => 'nullable|url|max:255',
            'phone_number' => 'nullable|string|max:50',
            'footer_description' => 'nullable|string|max:500',
        ]);

        foreach (self::PLATFORMS as $platform) {
            SiteSetting::set('social_'.$platform, trim($validated['social_'.$platform] ?? ''));
        }

        SiteSetting::set('social_show_header', $request->boolean('social_show_header') ? '1' : '');
        SiteSetting::set('social_show_footer', $request->boolean('social_show_footer') ? '1' : '');
        SiteSetting::set('phone_number', trim($validated['phone_number'] ?? ''));
        SiteSetting::set('footer_description', trim($validated['footer_description'] ?? '') ?: self::DEFAULT_FOOTER_DESCRIPTION);

        return redirect()->route('admin.social.index')->with('status', 'Social links & contact info updated.');
    }
}
