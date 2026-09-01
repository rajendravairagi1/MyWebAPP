<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Broker extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'notes',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(BrokerTransaction::class)->latest('transaction_date')->latest('id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(BrokerDocument::class)->latest();
    }

    public function totalCommissionAccrued(): float
    {
        return (float) $this->transactions()->where('type', 'commission_accrued')->sum('amount');
    }

    public function totalPaid(): float
    {
        return (float) $this->transactions()->where('type', 'payment_paid')->sum('amount');
    }

    /**
     * What the business still owes this broker in unpaid brokerage.
     */
    public function balance(): float
    {
        return $this->totalCommissionAccrued() - $this->totalPaid();
    }
}
