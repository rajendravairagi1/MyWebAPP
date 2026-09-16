<?php

namespace App\Support;

use App\Models\Contractor;
use Illuminate\Support\Str;

/**
 * Resolves the contractor_id to save on a project cost entry — the same
 * "pick an existing one, or type a new one right here" pattern used for
 * brokers: a contractor shouldn't need to be set up separately before
 * you can pay them for the work that just happened.
 */
class ContractorResolver
{
    public static function resolve(array $data): ?int
    {
        if (filled($data['new_contractor_name'] ?? null)) {
            $type = $data['new_contractor_type'] ?? 'other';
            if ($type === 'other' && filled($data['new_contractor_type_other'] ?? null)) {
                $type = $data['new_contractor_type_other'];
            }

            $name = $data['new_contractor_name'];
            $phone = $data['new_contractor_phone'] ?? null;

            // Same name AND same phone as an existing contractor almost
            // certainly means "same person, typed again" rather than a
            // second real person — auto-reuse that record instead of
            // fragmenting their payment history across duplicates. Name
            // matching alone isn't reused: names repeat across genuinely
            // different people far too often (e.g. common Indian names)
            // to treat that alone as proof of identity.
            if (filled($phone) && $existing = self::findByNameAndPhone($name, $phone)) {
                return $existing->id;
            }

            $contractor = Contractor::create([
                'name' => $name,
                'type' => $type,
                'phone' => $phone,
            ]);

            return $contractor->id;
        }

        return $data['contractor_id'] ?? null;
    }

    protected static function findByNameAndPhone(string $name, string $phone): ?Contractor
    {
        $normalizedPhone = self::normalizePhone($phone);

        if ($normalizedPhone === '') {
            return null;
        }

        return Contractor::whereRaw('LOWER(TRIM(name)) = ?', [Str::lower(trim($name))])
            ->get()
            ->first(fn (Contractor $contractor) => self::normalizePhone((string) $contractor->phone) === $normalizedPhone);
    }

    /**
     * Digits only, so "98765 43210", "+91 98765-43210" and "9876543210"
     * all compare equal — people rarely type a phone number the exact
     * same way twice.
     */
    protected static function normalizePhone(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone) ?? '';
    }
}
