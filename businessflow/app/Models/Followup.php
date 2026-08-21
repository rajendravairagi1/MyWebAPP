<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'project_id',
        'note',
        'due_at',
        'status',
        'owner_id',
    ];

    protected $casts = [
        'due_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function whatsappUrl(): ?string
    {
        $phone = $this->customer?->phone;

        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (strlen($digits) === 10) {
            $digits = '91'.$digits; // default to India country code for a bare 10-digit number
        }

        $message = "Hi {$this->customer->name}, {$this->note}";

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
