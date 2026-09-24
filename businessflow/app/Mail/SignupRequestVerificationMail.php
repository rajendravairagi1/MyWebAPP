<?php

namespace App\Mail;

use App\Models\SignupRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

/**
 * Sent the moment someone submits the public /get-started form — the
 * request itself stays in 'unverified' status (invisible to Platform
 * Admin's queue) until they click the link this carries. Confirms the
 * email is real and reachable before it ever reaches a human to review.
 */
class SignupRequestVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SignupRequest $signupRequest) {}

    public function build(): self
    {
        $url = URL::temporarySignedRoute(
            'signup-requests.public.verify',
            now()->addHours(24),
            ['signupRequest' => $this->signupRequest->id, 'hash' => sha1($this->signupRequest->email)]
        );

        return $this->subject(__('Confirm your email — :app', ['app' => config('app.name')]))
            ->markdown('emails.signup-requests.verify', [
                'name' => $this->signupRequest->name,
                'url' => $url,
            ]);
    }
}
