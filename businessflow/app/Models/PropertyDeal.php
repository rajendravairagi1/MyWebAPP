<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyDeal extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'property_title',
        'broker_id',
        'address',
        'seller_name',
        'seller_phone',
        'purchase_price',
        'asking_price',
        'buyer_name',
        'buyer_phone',
        'sale_price',
        'status',
        'deal_date',
        'sold_date',
        'notes',
        'contact_name',
        'contact_phone',
        'contact_email',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'asking_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'deal_date' => 'date',
        'sold_date' => 'date',
    ];

    /**
     * Null until a sale price is actually entered — an open deal has no
     * profit yet, it isn't zero.
     */
    public function profit(): ?float
    {
        if ($this->sale_price === null) {
            return null;
        }

        return (float) $this->sale_price - (float) $this->purchase_price;
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'sold' => 'Sold',
            'cancelled' => 'Cancelled',
            default => 'Open',
        };
    }

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    /**
     * The token used in this deal's public, no-login share link —
     * generated once on first use and kept stable after that so a link
     * already handed to a customer never breaks.
     */
    public function shareToken(): string
    {
        if (! $this->share_token) {
            $this->share_token = \Illuminate\Support\Str::random(32);
            $this->save();
        }

        return $this->share_token;
    }

    public function media(): HasMany
    {
        return $this->hasMany(PropertyDealMedia::class)->latest();
    }

    public function photos(): HasMany
    {
        return $this->media()->where('type', 'photo');
    }

    public function layouts(): HasMany
    {
        return $this->media()->where('type', 'layout');
    }

    public function documents(): HasMany
    {
        return $this->media()->where('type', 'document');
    }
}
