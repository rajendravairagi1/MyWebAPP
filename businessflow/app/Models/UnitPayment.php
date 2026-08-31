<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitPayment extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_unit_id',
        'customer_id',
        'loan_id',
        'amount',
        'purpose',
        'description',
        'method',
        'paid_at',
        'reference',
        'notes',
        'recorded_by',
    ];

    public const PURPOSES = [
        'token' => 'Token / Booking Amount',
        'installment' => 'Installment',
        'registry' => 'Registry / Stamp Duty',
        'maintenance' => 'Maintenance',
        'other' => 'Other',
    ];

    public function purposeLabel(): string
    {
        return self::PURPOSES[$this->purpose] ?? ($this->purpose ?: 'Payment');
    }

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    /**
     * The receipt invoice auto-generated when this payment was recorded.
     * See UnitPaymentController::store().
     */
    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
