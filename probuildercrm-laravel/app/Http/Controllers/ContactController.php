<?php

namespace App\Http\Controllers;

use App\Mail\NewDemoRequest;
use App\Models\ContactSubmission;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'recaptchaSiteKey' => SiteSetting::get('recaptcha_site_key'),
            'plans' => PricingPlan::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Honeypot: a field real visitors never see or fill (hidden off-screen),
        // that spam bots filling every field on the page trip over. Pretend the
        // submission worked rather than telling the bot what caught it.
        if (filled($request->input('company_website'))) {
            return back()->with('status', 'sent');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'plan' => 'nullable|string|max:100',
            'message' => 'required|string|max:5000',
        ]);

        $secretKey = SiteSetting::get('recaptcha_secret_key');

        if ($secretKey && ! $this->recaptchaPassed($request, $secretKey)) {
            return back()
                ->withInput()
                ->withErrors(['recaptcha' => "Please tick the \"I'm not a robot\" checkbox."]);
        }

        $submission = ContactSubmission::create($validated);
        $this->notifyTeam($submission);

        return back()->with('status', 'sent');
    }

    /**
     * Emails whoever's configured under Admin > Integrations. Never blocks
     * the submission itself — a genuine demo request is still saved (and
     * visible under Admin > Demo Requests) even if mail delivery fails.
     */
    private function notifyTeam(ContactSubmission $submission): void
    {
        $emails = collect(explode(',', (string) SiteSetting::get('notification_emails')))
            ->map(fn ($email) => trim($email))
            ->filter()
            ->all();

        if (empty($emails)) {
            return;
        }

        try {
            Mail::to($emails)->send(new NewDemoRequest($submission));
        } catch (\Throwable $e) {
            Log::warning('Failed to send demo request notification email: '.$e->getMessage());
        }
    }

    /**
     * Verifies the widget's response token server-side with Google —
     * the checkbox alone is client-side and trivially spoofable without
     * this, since a bot can just POST the form directly.
     */
    private function recaptchaPassed(Request $request, string $secretKey): bool
    {
        $token = (string) $request->input('g-recaptcha-response');

        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $request->ip(),
            ]);

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable) {
            // Google's verification endpoint being unreachable shouldn't
            // be the reason a genuine customer's demo request is lost —
            // fail open rather than blocking every submission.
            return true;
        }
    }
}
