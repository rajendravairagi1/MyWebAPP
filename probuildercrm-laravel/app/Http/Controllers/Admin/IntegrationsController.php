<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewDemoRequest;
use App\Models\ContactSubmission;
use App\Models\SiteSetting;
use App\Support\SitemapPing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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
            'analyticsScript' => SiteSetting::get('analytics_script'),
            'sitemapLastPingedAt' => SiteSetting::get('sitemap_last_pinged_at'),
            'notificationEmails' => SiteSetting::get('notification_emails'),
        ]);
    }

    public function pingSitemap()
    {
        SitemapPing::ping();

        return redirect()->route('admin.integrations.index')->with('status', 'Sitemap submitted to search engines.');
    }

    /**
     * Sends a real email through the exact same Mailable/mailer the real
     * "new demo request" notification uses — the notification_emails
     * setting being correctly filled in doesn't mean mail actually goes
     * out; MAIL_MAILER can still be unset/misconfigured on the server
     * (defaults to "log", which just writes to a file and sends nothing).
     * This is the fastest way to tell "not configured" apart from
     * "configured but not working" without SSH access to check .env
     * or the log file directly.
     */
    public function sendTestEmail()
    {
        $emails = collect(explode(',', (string) SiteSetting::get('notification_emails')))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->all();

        if (empty($emails)) {
            return redirect()->route('admin.integrations.index')
                ->withErrors(['test_email' => 'Add an email under "Notify these emails" first, then save, before sending a test.']);
        }

        // The "log"/"array" mailers never throw — they just write the
        // email to a file or discard it, which would make this test
        // falsely report success. Catch that case explicitly rather than
        // attempting a send that can't actually fail.
        if (in_array(config('mail.default'), ['log', 'array', 'null'], true)) {
            return redirect()->route('admin.integrations.index')
                ->withErrors(['test_email' => 'No real mailer is configured on the server (MAIL_MAILER is set to "'.config('mail.default').'", which never actually sends). Ask your host or developer to set MAIL_MAILER=smtp plus MAIL_HOST/MAIL_USERNAME/MAIL_PASSWORD in the .env file.']);
        }

        $fakeSubmission = new ContactSubmission([
            'name' => 'Test Notification',
            'email' => 'test@example.com',
            'phone' => null,
            'plan' => null,
            'message' => 'This is a test email to confirm demo request notifications are actually being delivered.',
        ]);
        $fakeSubmission->created_at = now();

        try {
            Mail::to($emails)->send(new NewDemoRequest($fakeSubmission));
        } catch (\Throwable $e) {
            return redirect()->route('admin.integrations.index')
                ->withErrors(['test_email' => 'Sending failed: '.$e->getMessage().' — this usually means the mailer isn\'t configured on the server (MAIL_MAILER/MAIL_HOST/MAIL_USERNAME/MAIL_PASSWORD in .env). Contact your host or developer to set up SMTP.']);
        }

        return redirect()->route('admin.integrations.index')
            ->with('status', 'Test email sent to '.implode(', ', $emails).' — check the inbox (and spam folder) in a minute. If it never arrives despite no error here, the mail server accepted it but something downstream (spam filter, wrong SMTP "from" domain) is dropping it.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'recaptcha_site_key' => ['nullable', 'string', 'max:255'],
            'recaptcha_secret_key' => ['nullable', 'string', 'max:255'],
            'analytics_script' => ['nullable', 'string', 'max:5000'],
            'notification_emails' => ['nullable', 'string', 'max:500', function ($attribute, $value, $fail) {
                foreach (explode(',', (string) $value) as $email) {
                    $email = trim($email);
                    if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $fail("\"{$email}\" is not a valid email address.");
                    }
                }
            }],
        ]);

        SiteSetting::set('recaptcha_site_key', trim($validated['recaptcha_site_key'] ?? ''));
        SiteSetting::set('recaptcha_secret_key', trim($validated['recaptcha_secret_key'] ?? ''));
        SiteSetting::set('analytics_script', trim($validated['analytics_script'] ?? ''));
        SiteSetting::set('notification_emails', trim($validated['notification_emails'] ?? ''));

        return redirect()->route('admin.integrations.index')->with('status', 'Integrations updated.');
    }
}
