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
        'type',
        'bank_name',
        'account_number',
        'notes',
    ];

    /**
     * 'bank' = an actual account money lands in (bank transfer, UPI,
     * cheque, card). 'cash' = a person who's physically holding cash —
     * there's no account for it to land in, only who has it. A Cash
     * payment can only be assigned to a 'cash' account and vice versa,
     * so the two never get mixed up in the dropdowns.
     */
    public const TYPES = [
        'bank' => 'Bank / digital account',
        'cash' => 'Cash-in-hand (person holding it)',
    ];

    public function isCash(): bool
    {
        return $this->type === 'cash';
    }

    /**
     * Bank + last-4 appended so accounts sharing a name (Priya's three
     * accounts) are still distinguishable everywhere this shows up —
     * a dropdown, a payment row, an ITR export.
     */
    public function label(): string
    {
        if ($this->isCash()) {
            return $this->name.' ('.__('Cash-in-hand').')';
        }

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
