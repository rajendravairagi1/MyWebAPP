<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A rate-based (or flat) contract given to a Contractor for a specific
 * scope of work on a Project — e.g. "Plastering, Ground Floor: 1000 sqft
 * @ ₹200/sqft = ₹200,000". Payments against it stay as ProjectCost rows
 * (nothing duplicated here, same pattern as Contractor) — this only
 * records what was AGREED, so balance() can answer "how much of this
 * contract is still owed" instead of just "how much have I paid this
 * contractor overall".
 */
class WorkOrder extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_id',
        'contractor_id',
        'description',
        'area_sqft',
        'rate_per_sqft',
        'total_amount',
        'notes',
    ];

    protected $casts = [
        'area_sqft' => 'decimal:2',
        'rate_per_sqft' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ProjectCost::class)->latest('spent_on')->latest('id');
    }

    /**
     * Money that has actually left an account against this work order —
     * same "paid immediately, or credit since settled" rule as
     * Contractor::totalPaid(), just scoped to this one contract instead
     * of everything ever paid to the contractor.
     */
    public function totalPaid(): float
    {
        return (float) $this->payments()
            ->where(function ($q) {
                $q->where('is_credit', false)->orWhereNotNull('credit_settled_at');
            })
            ->sum('amount');
    }

    /**
     * What's left to pay on this contract. Can go negative if more was
     * paid than agreed — shown as-is rather than clamped, so an
     * overpayment is visible instead of silently hidden.
     */
    public function balance(): float
    {
        return (float) $this->total_amount - $this->totalPaid();
    }
}
