<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    /**
     * Human-readable labels for each module permission, used to render the checkbox list.
     * Keep this in sync with database/seeders/RolesAndModulesSeeder.php.
     */
    protected function moduleLabels(): array
    {
        return [
            'dashboard-owner' => 'Owner Dashboard',
            'dashboard-general' => 'General Dashboard',
            'crm-customers' => 'Customer Management (CRM)',
            'job-work' => 'Job Work',
            'store-material' => 'Store & Material Issue',
            'production' => 'Production Stage Tracking',
            'quality' => 'Quality Check (QC)',
            'dispatch' => 'Dispatch',
            'security-gate' => 'Security Gate',
            'invoice-accounts' => 'Invoice & Account Ledger',
            'purchase' => 'Purchase',
            'hr-employees' => 'HR — Employees',
            'attendance' => 'Attendance',
            'payroll' => 'Payroll & Salary Slips',
            'reports' => 'Reports Center',
            'bd-crm' => 'Business Development',
            'role-permissions' => 'Manage Roles & Permissions',
        ];
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->orderBy('name')->get();

        return view('roles.index', ['roles' => $roles]);
    }

    public function edit(Role $role)
    {
        $modules = $this->moduleLabels();
        $granted = $role->permissions->pluck('name')
            ->map(fn ($name) => str_replace('module.', '', $name))
            ->all();

        return view('roles.edit', [
            'role' => $role,
            'modules' => $modules,
            'granted' => $granted,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'modules' => ['array'],
            'modules.*' => ['string'],
        ]);

        $moduleKeys = array_keys($this->moduleLabels());
        $selected = array_values(array_intersect($validated['modules'] ?? [], $moduleKeys));

        foreach ($selected as $key) {
            Permission::firstOrCreate(['name' => "module.$key", 'guard_name' => 'web']);
        }

        $role->syncPermissions(
            collect($selected)->map(fn ($m) => "module.$m")
        );

        return redirect()
            ->route('roles.edit', $role)
            ->with('status', "{$role->name} ka access update ho gaya.");
    }
}
