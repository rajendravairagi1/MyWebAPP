<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Investor extends Model
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
        return $this->hasMany(InvestorTransaction::class)->latest('transaction_date')->latest('id');
    }

    public function totalInvested(): float
    {
        return (float) $this->transactions()->where('type', 'investment')->sum('amount');
    }

    public function totalPaidOut(): float
    {
        return (float) $this->transactions()->where('type', 'payout')->sum('amount');
    }

    /**
     * What the business still owes this investor — principal not yet
     * returned plus any profit not yet paid out. Investments raise it,
     * payouts lower it; nothing here assumes principal ever comes back
     * as a lump sum, since that's between the builder and the investor.
     */
    public function balance(): float
    {
        return $this->totalInvested() - $this->totalPaidOut();
    }
}
