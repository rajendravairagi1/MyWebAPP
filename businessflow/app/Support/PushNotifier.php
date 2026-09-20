<?php

namespace App\Support;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Sends a real phone notification (meeting/follow-up/payment reminder)
 * to every device a user has subscribed from, via the Web Push protocol
 * — this is what makes an alert land even when the app isn't open. A
 * subscription the push service reports as gone (uninstalled app,
 * revoked permission, expired) is deleted so it's never retried.
 */
class PushNotifier
{
    public static function send(User $user, string $title, string $body, ?string $url = null): void
    {
        $subscriptions = $user->pushSubscriptions()->get();

        if ($subscriptions->isEmpty() || ! config('services.web_push.public_key')) {
            return;
        }

        // minishlink/web-push emits a harmless E_USER_NOTICE when neither
        // the GMP nor BCMath extension is available ("fastest calculator
        // not found" — it still works, just slower) — Laravel's default
        // error handler turns that notice into a fatal ErrorException, so
        // this silences it for the duration of the send rather than
        // depending on every production host having one of those
        // extensions enabled.
        set_error_handler(fn () => true, E_USER_NOTICE | E_NOTICE | E_USER_WARNING | E_WARNING);

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('services.web_push.subject'),
                    'publicKey' => config('services.web_push.public_key'),
                    'privateKey' => config('services.web_push.private_key'),
                ],
            ]);

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'url' => $url ?: url('/dashboard'),
            ]);

            foreach ($subscriptions as $subscription) {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'publicKey' => $subscription->public_key,
                        'authToken' => $subscription->auth_token,
                        'contentEncoding' => $subscription->content_encoding,
                    ]),
                    $payload
                );
            }

            foreach ($webPush->flush() as $report) {
                if (! $report->isSuccess() && $report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint', $report->getRequest()->getUri())->delete();
                }
            }
        } catch (\Throwable $e) {
            // A single malformed/corrupted subscription (or a push
            // service outage) must never take down the whole reminder
            // sweep for every other business — log it and move on.
            report($e);
        } finally {
            restore_error_handler();
        }
    }
}
