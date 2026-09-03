<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * A phone number is typed in all sorts of formats — spaces, dashes,
 * parentheses, a leading + for a country code — so this doesn't force
 * one exact shape. It only rejects what clearly isn't a phone number at
 * all: letters, symbols, or too few/many digits to ever be a real one.
 * Used everywhere a "phone"/"mobile" field is collected, so a customer
 * typing a name into the phone box (or vice versa) is caught here
 * instead of surfacing as a broken WhatsApp link or a bounced call
 * later on.
 */
class Phone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            $fail('The :attribute must be a valid phone number.');

            return;
        }

        if (! preg_match('/^[0-9+\-\s().]+$/', $value)) {
            $fail('The :attribute may only contain numbers, spaces, and + - ( ).');

            return;
        }

        $digitCount = strlen(preg_replace('/\D/', '', $value));

        if ($digitCount < 7 || $digitCount > 15) {
            $fail('The :attribute must be a valid phone number.');
        }
    }
}
