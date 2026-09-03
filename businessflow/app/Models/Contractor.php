<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A labor contractor, vendor/shop, or trade (painter, plumber, tiles,
 * electrician, fabrication, POP...) that a project pays repeatedly.
 * Payments themselves stay as ProjectCost rows (nothing duplicated
 * here) — this is just the "who", so every payment ever made to Ram
 * or to a shop can be pulled up in one place instead of hunting
 * through each project's cost list by matching a free-text name.
 */
class Contractor extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'type',
        'phone',
        'email',
        'notes',
    ];

    public const TYPES = [
        'labor' => 'Labor Contractor',
        'vendor' => 'Vendor / Supplier',
        'painter' => 'Painter',
        'plumber' => 'Plumber',
        'electrician' => 'Electrician',
        'tiles' => 'Tiles',
        'fabrication' => 'Fabrication',
        'pop' => 'POP',
        'other' => 'Other',
    ];

    public function costs(): HasMany
    {
        return $this->hasMany(ProjectCost::class)->latest('spent_on')->latest('id');
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Money that has actually left an account for this contractor —
     * paid immediately, or credit that's since been settled. Excludes
     * outstanding "udhar" (see ProjectCost::isOutstandingCredit()).
     */
    public function totalPaid(): float
    {
        return (float) $this->costs()
            ->where(function ($q) {
                $q->where('is_credit', false)->orWhereNotNull('credit_settled_at');
            })
            ->sum('amount');
    }

    /**
     * Material/labor taken on credit from this contractor, not yet paid.
     */
    public function totalOutstanding(): float
    {
        return (float) $this->costs()
            ->where('is_credit', true)
            ->whereNull('credit_settled_at')
            ->sum('amount');
    }

    /**
     * Everything ever recorded against this contractor, paid or not —
     * the total value of work/material they've provided across projects.
     */
    public function grandTotal(): float
    {
        return (float) $this->costs()->sum('amount');
    }
}
