<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'sku',
        'type',
        'unit',
        'price',
        'tax_rate',
        'stock_qty',
        'low_stock_threshold',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public function isLowStock(): bool
    {
        return $this->stock_qty !== null
            && $this->low_stock_threshold !== null
            && $this->stock_qty <= $this->low_stock_threshold;
    }
}
