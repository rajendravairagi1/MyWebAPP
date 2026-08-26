<?php

namespace App\Support;

/**
 * Holds the active business_id (plus the current user's role/permissions
 * within it) for the current request only. Set exclusively by
 * App\Http\Middleware\IdentifyTenant from the authenticated user's
 * session + business_user membership row — never from client input.
 */
class Tenant
{
    protected static ?int $id = null;

    protected static ?string $role = null;

    protected static ?array $permissions = null;

    public static function set(?int $id, ?string $role = null, ?array $permissions = null): void
    {
        static::$id = $id;
        static::$role = $role;
        static::$permissions = $permissions;
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
        static::$role = null;
        static::$permissions = null;
    }

    public static function role(): ?string
    {
        return static::$role;
    }

    /**
     * Owners (and company owners, once that tier exists) always have
     * full access — module permissions only ever narrow a supervisor.
     */
    public static function isOwner(): bool
    {
        return in_array(static::$role, ['owner', 'company_owner'], true);
    }

    public static function can(string $module): bool
    {
        if (static::isOwner()) {
            return true;
        }

        return in_array($module, static::$permissions['modules'] ?? [], true);
    }
}
