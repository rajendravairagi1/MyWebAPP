<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndModulesSeeder extends Seeder
{
    /**
     * Every screen/module in the ERP. Each becomes a Spatie permission
     * named "module.<key>" so a role's access is just "does it have this permission".
     */
    protected array $modules = [
        'dashboard-owner' => 'Owner Dashboard (financials, profit, customer analytics)',
        'dashboard-general' => 'General Dashboard (today\'s snapshot)',
        'crm-customers' => 'Customer Management (CRM)',
        'job-work' => 'Job Work (receive, pending, working, ready)',
        'store-material' => 'Store & Material Issue',
        'production' => 'Production Stage Tracking',
        'quality' => 'Quality Check (QC)',
        'dispatch' => 'Dispatch',
        'security-gate' => 'Security Gate Verification',
        'invoice-accounts' => 'Invoice & Account Ledger',
        'purchase' => 'Purchase Module',
        'hr-employees' => 'HR — Employee Records',
        'attendance' => 'Attendance',
        'payroll' => 'Payroll & Salary Slips',
        'reports' => 'Reports Center',
        'bd-crm' => 'Business Development (leads, meetings, own customers)',
        'role-permissions' => 'Manage Roles & Permissions (Owner only, by default)',
    ];

    /**
     * Default access per role, matching BRD.md section 2-3.
     * Owner is granted every module in the seeder run below (not listed here).
     */
    protected array $roleDefaults = [
        'Plant Head' => ['dashboard-general', 'job-work', 'store-material', 'production', 'quality', 'dispatch', 'purchase', 'reports'],
        'HR Manager' => ['dashboard-general', 'hr-employees', 'attendance', 'payroll'],
        'Purchase Manager' => ['dashboard-general', 'purchase'],
        'Account Manager' => ['dashboard-general', 'invoice-accounts', 'purchase', 'payroll', 'reports'],
        'Store Person' => ['dashboard-general', 'store-material', 'job-work'],
        'Quality Person' => ['dashboard-general', 'quality'],
        'Security Guard' => ['dashboard-general', 'security-gate'],
        'Business Developer' => ['dashboard-general', 'bd-crm', 'crm-customers'],
        'Employee' => ['dashboard-general', 'attendance'],
    ];

    public function run(): void
    {
        foreach (array_keys($this->modules) as $key) {
            Permission::firstOrCreate(['name' => "module.$key", 'guard_name' => 'web']);
        }

        $owner = Role::firstOrCreate(['name' => 'Owner', 'guard_name' => 'web']);
        $owner->syncPermissions(
            collect(array_keys($this->modules))->map(fn ($m) => "module.$m")
        );

        foreach ($this->roleDefaults as $roleName => $moduleKeys) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions(
                collect($moduleKeys)->map(fn ($m) => "module.$m")
            );
        }
    }
}
