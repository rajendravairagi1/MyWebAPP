<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'supplier_id', 'raw_material_id', 'bill_number', 'purchase_date',
    'quantity', 'rate', 'amount', 'status', 'notes',
])]
class Purchase extends Model
{
    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function rawMaterial(): BelongsTo
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(PurchasePayment::class)->latest('paid_at');
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    public function balanceDue(): float
    {
        return round($this->amount - $this->totalPaid(), 2);
    }
}
