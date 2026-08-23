<?php

namespace App\Models\Concerns;

/**
 * Builds a wa.me deep link for a model that has a `customer()` relation
 * with a phone number. This opens WhatsApp with a pre-filled message —
 * WhatsApp's free chat links don't support attaching a file directly, so
 * any document to share has to be a link inside the message text.
 */
trait HasWhatsAppLink
{
    public function whatsappUrl(string $message): ?string
    {
        $phone = $this->customer?->phone;

        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) === 10) {
            $digits = '91'.$digits; // default to India country code for a bare 10-digit number
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
