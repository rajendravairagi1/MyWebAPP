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
     * Pass a $projectId to total just that project's payments instead
     * of every project this contractor has ever worked on.
     */
    public function totalPaid(?int $projectId = null): float
    {
        return (float) $this->costs()
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->where(function ($q) {
                $q->where('is_credit', false)->orWhereNotNull('credit_settled_at');
            })
            ->sum('amount');
    }

    /**
     * Material/labor taken on credit from this contractor, not yet paid.
     */
    public function totalOutstanding(?int $projectId = null): float
    {
        return (float) $this->costs()
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->where('is_credit', true)
            ->whereNull('credit_settled_at')
            ->sum('amount');
    }

    /**
     * Everything ever recorded against this contractor, paid or not —
     * the total value of work/material they've provided across projects
     * (or just one project, when $projectId is given).
     */
    public function grandTotal(?int $projectId = null): float
    {
        return (float) $this->costs()
            ->when($projectId, fn ($q) => $q->where('project_id', $projectId))
            ->sum('amount');
    }

    /**
     * Every project this contractor has at least one payment recorded
     * against — used to populate the "view by project" filter, so it
     * only ever lists projects actually relevant to this contractor.
     */
    public function projectsWorkedOn(): \Illuminate\Support\Collection
    {
        return Project::whereIn('id', $this->costs()->distinct()->pluck('project_id'))
            ->orderBy('name')
            ->get();
    }
}
