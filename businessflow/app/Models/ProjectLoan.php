<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * A construction loan the builder itself took out to fund a project -
 * see App\Models\Loan for the other direction (a customer's home loan
 * against one unit they're buying).
 *
 * Interest is accrued continuously on the outstanding principal at the
 * loan's annual rate (simple interest, prorated by the actual number of
 * days between events) rather than a fixed monthly amortization table.
 * This one model covers both repayment_type values without needing
 * different math for each: an "emi" loan is just one where the builder
 * happens to pay roughly the same amount on a regular schedule, and a
 * "full_payment" loan is one where interest-only payments (if any) keep
 * the accrued interest from piling up until the final payment clears
 * both remaining interest and the full principal - every payment is
 * applied interest-first, then whatever's left reduces principal, which
 * correctly represents both patterns from the same rule. repayment_type
 * is informational (shown on the loan, guides what a builder plans to
 * do) rather than something the math branches on.
 */
class ProjectLoan extends Model
{
    use BelongsToTenant, HasFactory;

    public const REPAYMENT_TYPES = [
        'emi' => 'Installments (EMI)',
        'full_payment' => 'Full Payment (Bullet)',
    ];

    protected $fillable = [
        'business_id',
        'project_id',
        'lender_name',
        'account_number',
        'principal_amount',
        'interest_rate',
        'repayment_type',
        'tenure_months',
        'disbursed_at',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'disbursed_at' => 'date',
        'account_number' => 'encrypted',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(ProjectLoanPayment::class)->orderBy('paid_at')->orderBy('id');
    }

    public function totalRepaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function totalInterestPaid(): float
    {
        return (float) $this->payments()->sum('interest_portion');
    }

    public function totalPrincipalPaid(): float
    {
        return (float) $this->payments()->sum('principal_portion');
    }

    public function outstandingPrincipal(): float
    {
        return max(0, (float) $this->principal_amount - $this->totalPrincipalPaid());
    }

    /**
     * Interest that has built up since the last payment (or since
     * disbursement, if none yet) but hasn't been collected in a payment
     * yet - "what today's interest bill would be if you closed this out
     * right now."
     */
    public function accruedInterestToDate(): float
    {
        $outstanding = $this->outstandingPrincipal();

        if ($outstanding <= 0 || (float) $this->interest_rate <= 0) {
            return 0;
        }

        $since = $this->payments()->max('paid_at') ?? $this->disbursed_at;
        $days = Carbon::parse($since)->diffInDays(now()->startOfDay());

        if ($days <= 0) {
            return 0;
        }

        return round($outstanding * ((float) $this->interest_rate / 100) * ($days / 365), 2);
    }

    /**
     * What it would take to fully close this loan today.
     */
    public function totalOutstanding(): float
    {
        return round($this->outstandingPrincipal() + $this->accruedInterestToDate(), 2);
    }

    public function isClosed(): bool
    {
        return $this->outstandingPrincipal() < 0.01;
    }

    /**
     * A ballpark EMI for display only ("aim to pay around ₹X/month") -
     * never used to compute actual payment splits, which are always
     * driven by the real payments recorded. Standard reducing-balance
     * EMI formula; returns null when there isn't enough to compute one
     * from (no tenure set, or a zero-interest loan where a plain
     * principal/tenure division is more honest than the formula, which
     * divides by zero at r=0).
     */
    public function suggestedMonthlyEmi(): ?float
    {
        if (! $this->tenure_months || $this->tenure_months <= 0) {
            return null;
        }

        $principal = (float) $this->principal_amount;
        $monthlyRate = (float) $this->interest_rate / 100 / 12;

        if ($monthlyRate <= 0) {
            return round($principal / $this->tenure_months, 2);
        }

        $factor = (1 + $monthlyRate) ** $this->tenure_months;

        return round($principal * $monthlyRate * $factor / ($factor - 1), 2);
    }

    /**
     * Recomputes every payment's interest/principal split in
     * chronological (paid_at) order, applied interest-first then
     * principal against the running outstanding balance — called after
     * any create/update/delete of a payment on this loan so the whole
     * history stays internally consistent even if a payment was added
     * out of order, backdated, or removed.
     *
     * Simplification: if a payment is smaller than the interest accrued
     * since the previous one, the whole payment counts as interest
     * (principal_portion stays 0) and the shortfall isn't carried
     * forward or compounded — the next payment's interest is calculated
     * fresh from its own date. For the EMI/interest-only-then-bullet
     * patterns this is built for, a payment covering less than the
     * accrued interest shouldn't normally happen; this is a deliberate
     * "good enough for a builder's own tracking" choice rather than
     * building full arrears/compounding, which a bank's own systems
     * already handle for the actual loan.
     */
    public function recalculateSplits(): void
    {
        $outstanding = (float) $this->principal_amount;
        $rate = (float) $this->interest_rate / 100;
        $since = $this->disbursed_at;

        foreach ($this->payments()->get() as $payment) {
            $days = max(0, Carbon::parse($since)->diffInDays($payment->paid_at));
            $interestDue = $rate > 0 ? round($outstanding * $rate * ($days / 365), 2) : 0.0;

            $interestPortion = min((float) $payment->amount, $interestDue);
            $principalPortion = round((float) $payment->amount - $interestPortion, 2);
            $principalPortion = min($principalPortion, $outstanding);

            $payment->forceFill([
                'interest_portion' => $interestPortion,
                'principal_portion' => $principalPortion,
            ])->saveQuietly();

            $outstanding = round($outstanding - $principalPortion, 2);
            $since = $payment->paid_at;
        }
    }
}
