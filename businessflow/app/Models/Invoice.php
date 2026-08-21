<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasLineItemTotals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use BelongsToTenant, HasFactory, HasLineItemTotals;

    protected $fillable = [
        'business_id',
        'customer_id',
        'project_id',
        'project_unit_id',
        'quotation_id',
        'number',
        'status',
        'due_date',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'amount_paid',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectUnit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function balanceDue(): string
    {
        return number_format(max(0, $this->total - $this->amount_paid), 2, '.', '');
    }

    public function recordPayment(float $amount, ?string $method, string $paidAt, ?string $reference, ?string $notes): Payment
    {
        $payment = $this->payments()->create([
            'business_id' => $this->business_id,
            'amount' => $amount,
            'method' => $method,
            'paid_at' => $paidAt,
            'reference' => $reference,
            'notes' => $notes,
            'recorded_by' => auth()->id(),
        ]);

        $this->refresh();
        $totalPaid = $this->payments()->sum('amount');

        $status = match (true) {
            $totalPaid <= 0 => $this->status === 'draft' ? 'draft' : 'sent',
            $totalPaid >= $this->total => 'paid',
            default => 'partially_paid',
        };

        $this->forceFill([
            'amount_paid' => $totalPaid,
            'status' => $status,
        ])->save();

        if ($status === 'paid' && $this->project_unit_id) {
            $this->projectUnit?->update(['status' => 'sold']);
        }

        return $payment;
    }

    public static function nextNumber(int $businessId): string
    {
        $business = Business::findOrFail($businessId);
        $count = static::withoutGlobalScope('tenant')->where('business_id', $businessId)->count();

        return sprintf('%s-%05d', $business->invoice_prefix ?: 'INV', $count + 1);
    }
}
