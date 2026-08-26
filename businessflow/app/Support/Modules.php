<?php

namespace App\Support;

/**
 * The toggleable modules an owner can grant a supervisor access to.
 * Dashboard, Profile, and Business Settings/Team aren't in this list —
 * Dashboard is always visible, and Business Settings/Team stay owner-only
 * regardless of what modules are granted (a supervisor should never be
 * able to widen their own access or change branding/billing).
 */
class Modules
{
    public const ALL = [
        'projects' => 'Projects',
        'customers' => 'Customers',
        'quotations' => 'Quotations',
        'invoices' => 'Invoices',
        'followups' => 'Follow-ups',
        'available_properties' => 'Available Properties',
        'ledger' => 'Ledger',
        'investors' => 'Investors',
        'completed_projects' => 'Completed Projects',
    ];
}
