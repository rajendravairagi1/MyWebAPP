<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyDeal extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'property_title',
        'address',
        'seller_name',
        'seller_phone',
        'purchase_price',
        'buyer_name',
        'buyer_phone',
        'sale_price',
        'status',
        'deal_date',
        'sold_date',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'deal_date' => 'date',
        'sold_date' => 'date',
    ];

    /**
     * Null until a sale price is actually entered — an open deal has no
     * profit yet, it isn't zero.
     */
    public function profit(): ?float
    {
        if ($this->sale_price === null) {
            return null;
        }

        return (float) $this->sale_price - (float) $this->purchase_price;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'sold' => 'Sold',
            'cancelled' => 'Cancelled',
            default => 'Open',
        };
    }
}
