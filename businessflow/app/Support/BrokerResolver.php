<?php

namespace App\Support;

use App\Models\Broker;

/**
 * Resolves the broker_id to save on a Property Deal or a property
 * assignment. The same "pick an existing one, or type a new one right
 * here" pattern used for accounts/projects elsewhere — a broker
 * shouldn't need to be set up separately before you can credit them
 * for the deal that just happened.
 */
class BrokerResolver
{
    public static function resolve(array $data): ?int
    {
        if (filled($data['new_broker_name'] ?? null)) {
            $broker = Broker::create([
                'name' => $data['new_broker_name'],
                'phone' => $data['new_broker_phone'] ?? null,
            ]);

            return $broker->id;
        }

        return $data['broker_id'] ?? null;
    }
}
