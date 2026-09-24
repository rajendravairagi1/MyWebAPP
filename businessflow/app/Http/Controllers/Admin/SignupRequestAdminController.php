<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SignupRequest;
use App\Support\DocumentQr;
use App\Support\SignupQrPoster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Platform Admin's queue of "I'd like an account" requests submitted
 * through the public /get-started form (see Public\SignupRequestController)
 * — the QR-code counterpart to a business's own Leads page, just one
 * level up: these become new customer accounts rather than leads inside
 * an existing one. Approving doesn't create the account by itself; it
 * hands off to the same "Add Customer Account" form already used for
 * manual entries (see AdminController::create), pre-filled with what the
 * customer typed, so the plan length/expiry is still always set by hand.
 */
class SignupRequestAdminController extends Controller
{
    public function index(): View
    {
        // 'unverified' requests never appear here at all — see
        // Public\SignupRequestController::verify(). An email nobody
        // confirmed never reaches this queue, reviewed or not.
        $pending = SignupRequest::where('status', 'pending')->latest()->get();
        $recent = SignupRequest::whereNotIn('status', ['pending', 'unverified'])->latest('reviewed_at')->limit(20)->get();

        $publicUrl = route('signup-requests.public.show');
        $posterUrl = route('admin.signup-requests.qr-poster');

        return view('admin.signup-requests', compact('pending', 'recent', 'publicUrl', 'posterUrl'));
    }

    public function qrPoster(): Response
    {
        $publicUrl = route('signup-requests.public.show');

        $png = SignupQrPoster::build($publicUrl) ?? DocumentQr::png($publicUrl, 460);

        abort_if(! $png, 404);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function reject(SignupRequest $signupRequest): RedirectResponse
    {
        abort_unless($signupRequest->isPending(), 422, 'This request was already reviewed.');

        $signupRequest->update(['status' => 'rejected', 'reviewed_at' => now()]);

        return back()->with('status', "Request from \"{$signupRequest->name}\" rejected.");
    }

    /**
     * Undoes a reject — moves a mistakenly-rejected request back into
     * "Awaiting Approval" so it can be approved after all. Only makes
     * sense for a rejected one: an already-approved request already has
     * its account created (see AdminController::store), so there's
     * nothing to "reopen" there.
     */
    public function reopen(SignupRequest $signupRequest): RedirectResponse
    {
        abort_unless($signupRequest->status === 'rejected', 422, 'Only a rejected request can be reopened.');

        $signupRequest->update(['status' => 'pending', 'reviewed_at' => null]);

        return back()->with('status', "Request from \"{$signupRequest->name}\" moved back to Awaiting Approval.");
    }
}
