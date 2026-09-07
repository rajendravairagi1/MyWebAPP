<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The public, no-login lead-intake form a QR code (or a plain shared
 * link) points a prospect at. Deliberately outside the tenant/auth
 * middleware — the business is identified by its lead_token or lead_slug,
 * not by a logged-in session — and a submission never lands directly in
 * the business's working Leads list: it's created with status "pending"
 * and only becomes an active lead once staff approve it from /leads.
 *
 * Two ways in: the original random lead_token (/l/{token}, kept working
 * indefinitely so an already-printed/shared link never breaks) and the
 * newer readable lead_slug (/{slug}/QRcode), which is what's now shown
 * and shared from the Leads page and Business Settings.
 */
class LeadFormController extends Controller
{
    public function show(string $token): View
    {
        $business = Business::where('lead_token', $token)->firstOrFail();

        return $this->renderForm($business, route('leads.public.store', $token));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $business = Business::where('lead_token', $token)->firstOrFail();

        return $this->handleSubmission($request, $business, route('leads.public.show', $token));
    }

    public function showBySlug(string $slug): View
    {
        $business = Business::where('lead_slug', $slug)->firstOrFail();

        return $this->renderForm($business, route('leads.public.store-slug', $slug));
    }

    public function storeBySlug(Request $request, string $slug): RedirectResponse
    {
        $business = Business::where('lead_slug', $slug)->firstOrFail();

        return $this->handleSubmission($request, $business, route('leads.public.show-slug', $slug));
    }

    private function renderForm(Business $business, string $formActionUrl): View
    {
        return view('leads.public-form', [
            'business' => $business,
            'formActionUrl' => $formActionUrl,
        ]);
    }

    private function handleSubmission(Request $request, Business $business, string $redirectUrl): RedirectResponse
    {
        // Honeypot: a real visitor never sees or fills this field (hidden
        // off-screen in the form view); a script filling every field blind
        // will. Silently pretend success rather than telling a bot it
        // tripped a filter.
        if (filled($request->input('hp_check_1'))) {
            return redirect($redirectUrl)->with('leadSubmitted', true);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        Lead::create([
            'business_id' => $business->id,
            'name' => $data['name'],
            'phone' => $data['phone'],
            'message' => $data['message'] ?? null,
            'source' => 'qr_form',
            'status' => Lead::STATUS_PENDING,
        ]);

        return redirect($redirectUrl)->with('leadSubmitted', true);
    }
}
