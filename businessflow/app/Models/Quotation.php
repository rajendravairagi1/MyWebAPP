<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\HasLineItemTotals;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quotation extends Model
{
    use BelongsToTenant, HasFactory, HasLineItemTotals;

    protected $fillable = [
        'business_id',
        'customer_id',
        'project_id',
        'project_unit_id',
        'number',
        'status',
        'valid_until',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'notes',
        'terms',
        'created_by',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(QuotationItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function convertToInvoice(): Invoice
    {
        abort_if($this->invoices()->exists(), 422, 'This quotation has already been converted to an invoice.');

        $invoice = Invoice::create([
            'business_id' => $this->business_id,
            'customer_id' => $this->customer_id,
            'project_id' => $this->project_id,
            'project_unit_id' => $this->project_unit_id,
            'quotation_id' => $this->id,
            'number' => Invoice::nextNumber($this->business_id),
            'status' => 'draft',
            'due_date' => now()->addDays(14),
            'created_by' => auth()->id(),
        ]);

        foreach ($this->items as $item) {
            $invoice->items()->create([
                'product_id' => $item->product_id,
                'description' => $item->description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount,
                'tax_rate' => $item->tax_rate,
            ]);
        }

        $invoice->recalculateTotals();

        $this->forceFill(['status' => 'accepted'])->save();

        return $invoice;
    }

    /**
     * Based on the highest number ever issued, not how many quotations
     * currently exist — see Invoice::nextNumber() for why a plain
     * count()+1 collides the moment a quotation that isn't the very
     * last one is ever deleted.
     */
    public static function nextNumber(int $businessId): string
    {
        $max = static::withoutGlobalScope('tenant')
            ->where('business_id', $businessId)
            ->where('number', 'like', 'QT-%')
            ->get(['number'])
            ->max(fn ($quotation) => (int) substr($quotation->number, 3));

        return sprintf('QT-%05d', ($max ?? 0) + 1);
    }
}
