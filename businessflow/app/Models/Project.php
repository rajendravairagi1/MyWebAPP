<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'type',
        'location',
        'status',
        'start_date',
        'expected_completion_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_completion_date' => 'date',
    ];

    public function costs(): HasMany
    {
        return $this->hasMany(ProjectCost::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProjectUnit::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }

    public function totalCost(): float
    {
        return (float) $this->costs()->sum('amount');
    }

    public function totalRevenue(): float
    {
        return (float) $this->invoices()->sum('amount_paid');
    }

    public function totalInvoiced(): float
    {
        return (float) $this->invoices()->sum('total');
    }

    public function profit(): float
    {
        return $this->totalRevenue() - $this->totalCost();
    }

    public function unitsSoldCount(): int
    {
        return $this->units()->where('status', 'sold')->count();
    }
}
