<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name', 'description', 'monthly_price', 'highlighted', 'sort_order', 'features'];

    protected $casts = [
        'highlighted' => 'boolean',
        'features' => 'array',
    ];
}
