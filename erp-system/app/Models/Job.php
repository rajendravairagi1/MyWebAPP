<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'customer_id', 'challan_number', 'vehicle_number', 'photo_path',
    'received_date', 'due_date', 'status', 'qc_status', 'qc_remarks', 'notes',
])]
class Job extends Model
{
    protected $table = 'job_orders';

    /**
     * Production flow, in order. "Quality Check" here is the in-line QC
     * step during production — see qc_status/qc_remarks for the pass/fail
     * decision recorded when that stage completes.
     */
    public const STAGES = [
        'Sanding',
        'Industrial Putty',
        'Fine Sanding',
        'Primer',
        'Drying',
        'Paint',
        'Heat/Oven',
        'Cooling',
        'Quality Check',
        'Wrapping',
        'Packing',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(JobItem::class, 'job_order_id');
    }

    public function stageLogs(): HasMany
    {
        return $this->hasMany(JobStageLog::class, 'job_order_id')->orderBy('id');
    }

    public function materialIssues(): HasMany
    {
        return $this->hasMany(MaterialIssue::class, 'job_order_id')->latest('issued_at');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'job_order_id');
    }

    public function securityGateLog(): HasOne
    {
        return $this->hasOne(SecurityGateLog::class, 'job_order_id');
    }

    public function completedStageCount(): int
    {
        return $this->stageLogs()->whereNotNull('completed_at')->count();
    }

    public function currentStage(): ?string
    {
        $index = $this->completedStageCount();

        return self::STAGES[$index] ?? null;
    }

    public function isProductionComplete(): bool
    {
        return $this->completedStageCount() >= count(self::STAGES);
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && ! in_array($this->status, ['ready', 'dispatched']);
    }
}
