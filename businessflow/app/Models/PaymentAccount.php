<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'bank_name',
        'account_number',
        'notes',
    ];

    /**
     * Bank + last-4 appended so accounts sharing a name (Priya's three
     * accounts) are still distinguishable everywhere this shows up —
     * a dropdown, a payment row, an ITR export.
     */
    public function label(): string
    {
        $extra = array_filter([$this->bank_name, $this->maskedAccountNumber()]);

        return $extra ? $this->name.' ('.implode(' ', $extra).')' : $this->name;
    }

    public function maskedAccountNumber(): ?string
    {
        if (! $this->account_number) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->account_number);

        return $digits && strlen($digits) > 4 ? '•••'.substr($digits, -4) : $this->account_number;
    }
}
