<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionRenewal extends Model
{
    protected $fillable = [
        'business_id',
        'company_id',
        'source',
        'plan',
        'previous_expires_at',
        'new_expires_at',
        'note',
    ];

    protected $casts = [
        'previous_expires_at' => 'date',
        'new_expires_at' => 'date',
    ];

    public const SOURCE_LABELS = [
        'admin_manual' => 'UPI / manual',
        'google_play' => 'Google Play',
        'trial_auto' => 'Auto trial (15-day)',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
