<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialEntry extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_unit_id',
        'material_name',
        'quantity',
        'unit_label',
        'direction',
        'entered_on',
        'note',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'entered_on' => 'date',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    public function signedQuantity(): float
    {
        return $this->direction === 'out' ? -(float) $this->quantity : (float) $this->quantity;
    }
}
