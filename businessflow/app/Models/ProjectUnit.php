<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUnit extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_id',
        'unit_number',
        'type',
        'area_sqft',
        'price',
        'status',
        'customer_id',
    ];

    protected $casts = [
        'area_sqft' => 'decimal:2',
        'price' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
