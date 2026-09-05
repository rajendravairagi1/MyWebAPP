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
    ];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function footerText(): string
    {
        return $this->footer_text ?: '© '.date('Y').' '.config('app.name').'. All rights reserved.';
    }
}
