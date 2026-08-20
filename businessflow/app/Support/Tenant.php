<?php

namespace App\Support;

/**
 * Holds the active business_id for the current request only.
 * Set exclusively by App\Http\Middleware\IdentifyTenant from the
 * authenticated user's session — never from client input.
 */
class Tenant
{
    protected static ?int $id = null;

    public static function set(?int $id): void
    {
        static::$id = $id;
    }

    public static function id(): ?int
    {
        return static::$id;
    }

    public static function check(): bool
    {
        return static::$id !== null;
    }

    public static function clear(): void
    {
        static::$id = null;
    }
}
