<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlayPurchase extends Model
{
    protected $fillable = [
        'business_id',
        'product_id',
        'purchase_token',
        'order_id',
        'status',
        'expiry_time',
        'acknowledged_at',
        'last_api_response',
    ];

    protected $casts = [
        'expiry_time' => 'datetime',
        'acknowledged_at' => 'datetime',
        'last_api_response' => 'array',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Whether a business should still be treated as paid-up right now —
     * "canceled" and "in_grace_period" both still hold access until
     * expiry_time, matching how a canceled-but-not-lapsed manual UPI
     * subscription already behaves.
     */
    public function grantsAccess(): bool
    {
        return in_array($this->status, ['active', 'canceled', 'in_grace_period'], true)
            && $this->expiry_time->isFuture();
    }
}
