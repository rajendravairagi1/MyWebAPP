<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestorTransaction extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'investor_id',
        'project_id',
        'type',
        'amount',
        'transaction_date',
        'method',
        'reference',
        'description',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function investor(): BelongsTo
    {
        return $this->belongsTo(Investor::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'investment' => 'Investment',
            'profit_credited' => 'Profit Credited',
            'payment_paid' => 'Payment Paid',
            default => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function detailsSummary(): string
    {
        return collect([
            $this->method ? ucfirst(str_replace('_', ' ', $this->method)) : null,
            $this->reference,
            $this->description,
        ])->filter()->implode(' · ');
    }
}
