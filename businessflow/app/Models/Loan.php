<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_unit_id',
        'customer_id',
        'bank_name',
        'loan_account_number',
        'sanctioned_amount',
        'sanctioned_at',
        'notes',
    ];

    protected $casts = [
        'sanctioned_amount' => 'decimal:2',
        'sanctioned_at' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Every bank tranche disbursed against this loan — each one is a
     * normal UnitPayment (so it counts toward the property's Collected
     * total like any other payment), just tagged with this loan_id.
     */
    public function disbursements(): HasMany
    {
        return $this->hasMany(UnitPayment::class)->latest('paid_at')->latest('id');
    }

    public function totalDisbursed(): float
    {
        return (float) $this->disbursements()->sum('amount');
    }

    public function remainingToDisburse(): float
    {
        return max(0, (float) $this->sanctioned_amount - $this->totalDisbursed());
    }

    public function percentDisbursed(): float
    {
        if ((float) $this->sanctioned_amount <= 0) {
            return 0;
        }

        return round(min(100, ($this->totalDisbursed() / (float) $this->sanctioned_amount) * 100), 1);
    }
}
