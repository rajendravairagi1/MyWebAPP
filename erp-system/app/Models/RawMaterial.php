<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category', 'unit', 'current_stock', 'low_stock_alert_at'])]
class RawMaterial extends Model
{
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->low_stock_alert_at;
    }
}
