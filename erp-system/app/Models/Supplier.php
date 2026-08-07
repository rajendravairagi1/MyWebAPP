<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'phone', 'gst_number', 'address', 'notes'])]
class Supplier extends Model
{
    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class)->latest('purchase_date');
    }

    public function totalPurchased(): float
    {
        return (float) $this->purchases()->sum('amount');
    }

    public function totalDue(): float
    {
        return round($this->purchases->sum(fn (Purchase $p) => $p->balanceDue()), 2);
    }
}
