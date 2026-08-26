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

    protected static ?string $plan = null;

    public static function set(?int $id, ?string $role = null, ?array $permissions = null, ?string $plan = null): void
    {
        static::$id = $id;
        static::$role = $role;
        static::$permissions = $permissions;
        static::$plan = $plan;
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
        static::$plan = null;
    }

    public static function role(): ?string
    {
        return static::$role;
    }

    public static function permissions(): ?array
    {
        return static::$permissions;
    }

    /**
     * Runs $callback with the tenant temporarily switched to $businessId
     * (full "owner" access, no permission restrictions), then restores
     * whatever was active before — used by Branch/Company dashboards to
     * compute another business's stats without permanently switching.
     */
    public static function runAs(int $businessId, callable $callback): mixed
    {
        $previousId = static::$id;
        $previousRole = static::$role;
        $previousPermissions = static::$permissions;
        $previousPlan = static::$plan;

        static::set($businessId, 'owner', null, 'company');

        try {
            return $callback();
        } finally {
            static::set($previousId, $previousRole, $previousPermissions, $previousPlan);
        }
    }

    /**
     * Owners, plus Company Owners and Branch Managers looking in from
     * above (see App\Models\Company / Branch), always have full access
     * within a business — module permissions only ever narrow a
     * regular supervisor.
     */
    public static function isOwner(): bool
    {
        return in_array(static::$role, ['owner', 'company_owner', 'branch_manager'], true);
    }

    public static function can(string $module): bool
    {
        if (static::isOwner()) {
            return true;
        }

        return in_array($module, static::$permissions['modules'] ?? [], true);
    }

    /**
     * Money-sensitive sub-permission for modules like Customers and
     * Projects, which mix non-sensitive info (contact details, unit
     * status) with sensitive financial info (price, payments, balance).
     * Always requires base module access first — financials narrows an
     * already-granted module, it never grants a module on its own.
     */
    public static function canFinancials(string $module): bool
    {
        if (static::isOwner()) {
            return true;
        }

        if (! static::can($module)) {
            return false;
        }

        return in_array($module, static::$permissions['financials'] ?? [], true);
    }

    /**
     * Whether the active business's subscription tier includes $tier
     * (solo < team < company) — gates the Team feature and the Company/
     * Branch hierarchy. Set from Business::effectivePlan() by
     * IdentifyTenant, so a business inside a branch is always 'company'
     * tier regardless of its own plan column.
     */
    public static function planAllows(string $tier): bool
    {
        $levels = ['solo' => 0, 'team' => 1, 'company' => 2];

        return ($levels[static::$plan ?? 'solo'] ?? 0) >= ($levels[$tier] ?? 0);
    }
}
