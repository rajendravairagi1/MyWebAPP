<?php

namespace App\Support;

use App\Models\Contractor;

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
            $contractor = Contractor::create([
                'name' => $data['new_contractor_name'],
                'type' => $data['new_contractor_type'] ?? 'other',
                'phone' => $data['new_contractor_phone'] ?? null,
            ]);

            return $contractor->id;
        }

        return $data['contractor_id'] ?? null;
    }
}
