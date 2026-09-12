<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A pending "I'd like an account" submission from the public
 * /get-started form — the QR-code counterpart to Leads, but platform-
 * wide rather than per-business, since nobody has a business yet at
 * this point. Never creates a User/Business by itself: Platform Admin
 * reviews each one and, on Approve, lands on the same account-creation
 * form already used for manually-added customers (see
 * Admin\AdminController::create), pre-filled with what was submitted
 * here, so the account still gets a plan length/expiry set by hand.
 */
class SignupRequest extends Model
{
    protected $fillable = [
        'name', 'phone', 'email', 'password_hash', 'plan', 'address',
        'status', 'business_id', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public const PLAN_LABELS = [
        'solo' => 'Solo',
        'team' => 'Builder + Team',
        'company' => 'Company',
    ];

    public function planLabel(): string
    {
        return self::PLAN_LABELS[$this->plan] ?? $this->plan;
    }
}
