<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class ProjectCost extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_id',
        'category',
        'description',
        'amount',
        'spent_on',
        'vendor',
        'contractor_id',
        'payment_account_id',
        'is_credit',
        'credit_settled_at',
        'notes',
        'bill_path',
        'bill_name',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_on' => 'date',
        'is_credit' => 'boolean',
        'credit_settled_at' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(PaymentAccount::class, 'payment_account_id');
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    /**
     * Material/labor taken on credit ("udhar") from a vendor, still
     * unpaid — no money has actually left any account for it yet.
     */
    public function isOutstandingCredit(): bool
    {
        return $this->is_credit && ! $this->credit_settled_at;
    }

    /**
     * The date money actually moved for this cost — the original spend
     * date normally, but the settlement date once a credit purchase is
     * paid off (that's when it really left the account).
     */
    public function moneyMovedOn(): Carbon
    {
        return $this->credit_settled_at ?? $this->spent_on;
    }
}
