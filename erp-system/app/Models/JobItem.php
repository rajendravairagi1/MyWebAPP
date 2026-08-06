<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['job_order_id', 'product_id', 'quantity'])]
class JobItem extends Model
{
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
