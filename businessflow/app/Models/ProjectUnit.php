<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectUnit extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'project_id',
        'unit_number',
        'type',
        'area_sqft',
        'price',
        'status',
        'customer_id',
        'commitment_date',
        'commitment_note',
        'archived_at',
        'write_off_amount',
        'write_off_note',
        'write_off_at',
    ];

    protected $casts = [
        'area_sqft' => 'decimal:2',
        'price' => 'decimal:2',
        'write_off_amount' => 'decimal:2',
        'commitment_date' => 'date',
        'archived_at' => 'datetime',
        'write_off_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(UnitPayment::class)->latest('paid_at')->latest('id');
    }

    public function materialEntries(): HasMany
    {
        return $this->hasMany(MaterialEntry::class)->latest('entered_on')->latest('id');
    }

    /**
     * Current stock per material at this unit — in minus out — for
     * whoever (a supervisor, usually) has been logging it. A unit
     * nobody has logged material for just has an empty list; nothing
     * else on the unit depends on this.
     *
     * @return \Illuminate\Support\Collection<int, array{material_name: string, unit_label: ?string, balance: float}>
     */
    public function materialStock(): \Illuminate\Support\Collection
    {
        return $this->materialEntries
            ->groupBy('material_name')
            ->map(fn ($entries) => [
                'material_name' => $entries->first()->material_name,
                'unit_label' => $entries->first()->unit_label,
                'balance' => $entries->sum(fn ($e) => $e->signedQuantity()),
            ])
            ->values();
    }

    public function totalInvoiced(): float
    {
        return (float) $this->invoices()->sum('total');
    }

    public function totalPaid(): float
    {
        return (float) $this->invoices()->sum('amount_paid');
    }

    public function balanceDue(): float
    {
        return max(0, $this->totalInvoiced() - $this->totalPaid());
    }

    /**
     * Money logged directly against this unit's sale (the customer-profile
     * "record payment" ledger), separate from formal invoice payments.
     */
    public function directPaid(): float
    {
        return (float) $this->payments()->sum('amount');
    }

    /**
     * All money actually received for this unit, regardless of whether it
     * came through a formal invoice or was logged directly.
     */
    public function totalCollected(): float
    {
        return $this->totalPaid() + $this->directPaid();
    }

    public function totalOutstanding(): float
    {
        return max(0, (float) $this->price - $this->totalCollected() - (float) $this->write_off_amount);
    }

    public function isArchived(): bool
    {
        return $this->archived_at !== null;
    }

    /**
     * Recomputes status/archived state from money actually received.
     * Call after every direct payment is recorded, edited, or removed.
     * A manual write-off holds its state until explicitly recovered —
     * this never auto-clears one.
     */
    public function syncPaymentState(): void
    {
        if ($this->write_off_at) {
            return;
        }

        $price = (float) $this->price;
        $collected = $this->totalCollected();

        if ($price > 0 && $collected >= $price) {
            $this->forceFill([
                'status' => 'sold',
                'archived_at' => $this->archived_at ?? now(),
            ])->save();

            $this->project?->syncCompletionStatus();

            return;
        }

        $this->forceFill([
            'status' => $collected > 0 ? 'booked' : ($this->status === 'sold' ? 'available' : $this->status),
            'archived_at' => null,
        ])->save();

        $this->project?->syncCompletionStatus();
    }

    public function writeOff(?string $note): void
    {
        $this->forceFill([
            'write_off_amount' => $this->totalOutstanding(),
            'write_off_note' => $note,
            'write_off_at' => now(),
            'archived_at' => now(),
        ])->save();

        $this->project?->syncCompletionStatus();
    }

    /**
     * Manually moves this unit back to active/open, still assigned to
     * whichever customer it was closed out with — e.g. a payment was
     * entered wrong and the builder needs it open again to fix it.
     * Deliberately does NOT call syncPaymentState(): a fully-paid unit
     * would just get immediately re-closed by that, right back to where
     * it started, since the money that's already logged still adds up
     * to the full price. Normal auto-close resumes on the next payment
     * add/edit/remove, once the figures are actually corrected.
     */
    public function recover(): void
    {
        $this->forceFill([
            'write_off_amount' => null,
            'write_off_note' => null,
            'write_off_at' => null,
            'archived_at' => null,
            'status' => $this->customer_id ? 'booked' : 'available',
        ])->save();

        $this->project?->syncCompletionStatus();
    }

    /**
     * Whether we delivered on the possession/handover date we promised —
     * 'met' or 'late' once the property is closed out, 'overdue' or
     * 'upcoming' while it's still open, or null if no date was set.
     */
    public function commitmentStatus(): ?string
    {
        if (! $this->commitment_date) {
            return null;
        }

        if ($this->isArchived()) {
            return $this->archived_at->toDateString() <= $this->commitment_date->toDateString() ? 'met' : 'late';
        }

        return $this->commitment_date->isPast() ? 'overdue' : 'upcoming';
    }
}
