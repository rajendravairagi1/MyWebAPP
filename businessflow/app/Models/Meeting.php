<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Meeting extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'project_id',
        'title',
        'location',
        'attendees',
        'notes',
        'scheduled_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isVideoLink(): bool
    {
        return (bool) preg_match('/^https?:\/\//i', (string) $this->location);
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

        $when = $this->scheduled_at->format('d M Y, h:i A');
        $message = "Hi {$this->customer->name}, reminder for our meeting \"{$this->title}\" on {$when}".($this->location ? " at {$this->location}" : '').'.';

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($message);
    }
}
