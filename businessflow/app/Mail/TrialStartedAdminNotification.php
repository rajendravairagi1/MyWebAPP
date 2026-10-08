<?php

namespace App\Mail;

use App\Models\Business;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * FYI-only, sent to Platform Admin the moment a verified signup starts
 * their own 15-day trial without waiting for manual approval (see
 * Public\SignupRequestController::verify() and
 * OnboardingController::store()). Nothing here is actionable by reply —
 * it just keeps Rajendra aware of who's in, so a trial can still be cut
 * short by hand from the business list if it looks wrong.
 */
class TrialStartedAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Business $business, public User $user) {}

    public function build(): self
    {
        return $this->subject('New 15-day trial started — '.$this->business->name)
            ->markdown('emails.trial-started-admin', [
                'business' => $this->business,
                'user' => $this->user,
            ]);
    }
}
