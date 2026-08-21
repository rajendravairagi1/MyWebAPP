<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'company',
        'phone',
        'email',
        'address',
        'notes',
        'source',
        'tags',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }
}
