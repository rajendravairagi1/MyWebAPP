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
        $directPaid = (float) UnitPayment::whereIn('project_unit_id', $this->units()->pluck('id'))->sum('amount');

        // Excludes the auto-generated receipt invoices created per direct
        // payment — that money is already counted in $directPaid above.
        return (float) $this->invoices()->whereNull('unit_payment_id')->sum('amount_paid') + $directPaid;
    }

    public function totalInvoiced(): float
    {
        return (float) $this->invoices()->whereNull('unit_payment_id')->sum('total');
    }

    public function profit(): float
    {
        return $this->totalRevenue() - $this->totalCost();
    }

    public function unitsSoldCount(): int
    {
        return $this->units()->where('status', 'sold')->count();
    }

    /**
     * Auto-flips status to "completed" once every unit is closed out (sold
     * & paid off, or written off) — nothing left open or for sale — and
     * back to "ongoing" the moment a unit is open again, e.g. a new one is
     * added or one is recovered from history. A project with no units at
     * all, or with a unit still being paid off, is left alone.
     */
    public function syncCompletionStatus(): void
    {
        if (! $this->units()->exists()) {
            return;
        }

        $hasOpenUnits = $this->units()->whereNull('archived_at')->exists();

        if (! $hasOpenUnits && $this->status !== 'completed') {
            $this->update(['status' => 'completed']);
        } elseif ($hasOpenUnits && $this->status === 'completed') {
            $this->update(['status' => 'ongoing']);
        }
    }
}
