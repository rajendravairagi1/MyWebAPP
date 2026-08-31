<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerTransaction extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'broker_id',
        'project_unit_id',
        'type',
        'amount',
        'commission_percent',
        'transaction_date',
        'method',
        'reference',
        'description',
        'recorded_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'commission_percent' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'commission_accrued' => 'Commission Earned',
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
