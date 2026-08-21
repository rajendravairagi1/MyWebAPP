<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCost extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_id',
        'category',
        'description',
        'amount',
        'spent_on',
        'vendor',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'spent_on' => 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
