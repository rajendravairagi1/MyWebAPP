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
        'unit_payment_id',
        'counts_toward_property_price',
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
        'counts_toward_property_price' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function unitPayment(): BelongsTo
    {
        return $this->belongsTo(UnitPayment::class);
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

    public function recordPayment(float $amount, ?string $method, string $paidAt, ?string $reference, ?string $notes, ?int $paymentAccountId = null): Payment
    {
        $isFirstPayment = ! $this->payments()->exists();

        $payment = $this->payments()->create([
            'business_id' => $this->business_id,
            'amount' => $amount,
            'method' => $method,
            'payment_account_id' => $paymentAccountId,
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

        // A unit only becomes "booked" once actual money (the token
        // payment) has come in — creating the invoice/quotation alone
        // is just a proposal and shouldn't reserve the property.
        if ($this->project_unit_id) {
            if ($status === 'paid') {
                $this->projectUnit?->update(['status' => 'sold']);
            } elseif ($isFirstPayment) {
                ProjectUnit::where('id', $this->project_unit_id)
                    ->where('status', 'available')
                    ->update(['status' => 'booked', 'customer_id' => $this->customer_id]);
            }
        }

        return $payment;
    }

    /**
     * Based on the highest number ever issued, not how many invoices
     * currently exist — a plain count() collides the moment any invoice
     * isn't the very last one ever deleted (e.g. INV-00002 deleted out
     * of 00001/00002/00003 makes count() 2, so "count()+1" would try to
     * reissue 00003, which already exists, throwing a raw unique-
     * constraint 500). This also covers the receipt invoice for a
     * payment, which deletes itself automatically (cascadeOnDelete) the
     * moment that payment is removed.
     */
    public static function nextNumber(int $businessId): string
    {
        $business = Business::findOrFail($businessId);
        $prefix = $business->invoice_prefix ?: 'INV';

        $max = static::withoutGlobalScope('tenant')
            ->where('business_id', $businessId)
            ->where('number', 'like', $prefix.'-%')
            ->get(['number'])
            ->max(fn ($invoice) => (int) substr($invoice->number, strlen($prefix) + 1));

        return sprintf('%s-%05d', $prefix, ($max ?? 0) + 1);
    }
}
