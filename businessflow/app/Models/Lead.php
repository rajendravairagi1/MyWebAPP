<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use BelongsToTenant;

    /**
     * pending sits outside the pipeline proper — it's the "came in through
     * the public form, not reviewed yet" holding state. A lead only starts
     * moving through STAGES (new onward) once a staff member approves it,
     * so a stranger scanning a printed QR code never lands directly in the
     * working Leads list.
     */
    const STATUS_PENDING = 'pending';

    const STAGES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'site_visit_scheduled' => 'Site Visit Scheduled',
        'site_visit_done' => 'Site Visit Done',
        'quotation_sent' => 'Quotation Sent',
        'negotiation' => 'Negotiation',
        'booked' => 'Booked',
    ];

    const REJECTED = 'rejected';

    const LOST = 'lost';

    protected $fillable = [
        'business_id',
        'name',
        'phone',
        'email',
        'message',
        'source',
        'status',
        'notes',
        'converted_customer_id',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class);
    }

    public function convertedCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'converted_customer_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function stageLabel(): string
    {
        if ($this->status === self::REJECTED) {
            return 'Rejected';
        }
        if ($this->status === self::LOST) {
            return 'Lost';
        }

        return self::STAGES[$this->status] ?? $this->status;
    }

    public function whatsappUrl(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $this->phone);

        if (strlen($digits) === 10) {
            $digits = '91'.$digits; // default to India country code for a bare 10-digit number
        }

        return 'https://wa.me/'.$digits;
    }
}
