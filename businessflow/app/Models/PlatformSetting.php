<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A single settings row for the whole install (id 1, created on first
 * access) — the platform owner's own footer credit line and support
 * WhatsApp number, editable from the Platform Admin panel rather than
 * hardcoded, since only they should ever need to change these.
 */
class PlatformSetting extends Model
{
    protected $fillable = [
        'footer_text',
        'support_whatsapp',
        'payment_qr_path',
        'payment_upi_id',
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function footerText(): string
    {
        return $this->footer_text ?: '© '.date('Y').' '.config('app.name').'. All rights reserved.';
    }

    public function hasPaymentQr(): bool
    {
        return (bool) $this->payment_qr_path;
    }

    public function hasUpiId(): bool
    {
        return (bool) $this->payment_upi_id;
    }

    /**
     * A upi://pay deep link built fresh from the current UPI ID every
     * time it's called — on mobile this opens the visitor's UPI app
     * directly (GPay/PhonePe/Paytm/…) with the payee prefilled, no QR
     * scan needed. The QR code shown alongside it (see App\Support\
     * DocumentQr) encodes this exact same string, so both are always
     * in sync with whatever UPI ID is currently saved here.
     */
    public function upiPaymentLink(): ?string
    {
        if (! $this->payment_upi_id) {
            return null;
        }

        return 'upi://pay?'.http_build_query([
            'pa' => $this->payment_upi_id,
            'pn' => config('app.name'),
            'cu' => 'INR',
        ]);
    }
}
