<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $company = $request->user()->ownedCompany;
        abort_unless($company, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['nullable', 'email', 'max:255'],
            'manager_password' => ['nullable', 'string', 'min:8'],
        ]);

        $managerId = $this->resolveManager($data);

        $company->branches()->create([
            'name' => $data['name'],
            'manager_user_id' => $managerId,
        ]);

        return back()->with('status', 'Branch added.');
    }

    public function show(Request $request, Branch $branch): View
    {
        $this->authorizeBranch($request, $branch);

        $branch->load(['manager', 'company', 'businesses' => fn ($q) => $q->orderBy('created_at')]);

        $businessStats = $branch->businesses->mapWithKeys(fn ($business) => [$business->id => $business->statsSummary()]);
        $isCompanyOwner = $request->user()->ownedCompany?->id === $branch->company_id;

        $branchTotals = ['projects' => 0, 'customers' => 0, 'value' => 0, 'collected' => 0, 'outstanding' => 0, 'cost' => 0, 'profit' => 0];
        foreach ($businessStats as $stats) {
            foreach ($stats as $key => $value) {
                $branchTotals[$key] += $value;
            }
        }

        // Branch-wide totals mix every builder together — assumes one
        // currency per branch (see CompanyController::show()).
        $branchCurrencySymbol = \App\Models\Business::symbolFor($branch->businesses->first()?->currency);

        return view('branches.show', compact('branch', 'businessStats', 'isCompanyOwner', 'branchTotals', 'branchCurrencySymbol'));
    }

    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $company = $request->user()->ownedCompany;
        abort_unless($company && $branch->company_id === $company->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_name' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['nullable', 'email', 'max:255'],
            'manager_password' => ['nullable', 'string', 'min:8'],
        ]);

        $previousManagerId = $branch->manager_user_id;
        $newManagerId = $this->resolveManager($data) ?? $previousManagerId;

        $branch->update([
            'name' => $data['name'],
            'manager_user_id' => $newManagerId,
        ]);

        if ($newManagerId !== $previousManagerId) {
            $this->syncManagerAccess($branch, $previousManagerId, $newManagerId);
        }

        return back()->with('status', 'Branch updated.');
    }

    /**
     * Finds-or-creates the branch manager account from the submitted
     * fields (same pattern as Team's add-member flow). Returns null if
     * no manager fields were filled in — leaves the branch unmanaged.
     */
    private function resolveManager(array $data): ?int
    {
        if (empty($data['manager_email'])) {
            return null;
        }

        $user = User::where('email', $data['manager_email'])->first();

        if (! $user) {
            $user = User::create([
                'name' => $data['manager_name'] ?: $data['manager_email'],
                'email' => $data['manager_email'],
                'password' => bcrypt($data['manager_password'] ?: str()->random(16)),
            ]);
        }

        return $user->id;
    }

    /**
     * When a branch's manager changes, every builder already in that
     * branch needs its auto-granted 'branch_manager' access moved from
     * the old manager to the new one — new builders pick up the current
     * manager automatically when created (see BuilderController).
     */
    private function syncManagerAccess(Branch $branch, ?int $previousManagerId, ?int $newManagerId): void
    {
        foreach ($branch->businesses as $business) {
            if ($previousManagerId) {
                $existing = $business->users()->where('users.id', $previousManagerId)->first();

                // Only remove if their access here came from the branch
                // manager auto-grant — never touch a distinct local Team
                // membership they might separately have on this builder.
                if ($existing && $existing->pivot->role === 'branch_manager') {
                    $business->users()->detach($previousManagerId);
                }
            }

            if ($newManagerId && ! $business->users()->where('users.id', $newManagerId)->exists()) {
                $business->users()->attach($newManagerId, [
                    'role' => 'branch_manager',
                    'permissions' => null,
                    'status' => 'active',
                ]);
            }
        }
    }

    private function authorizeBranch(Request $request, Branch $branch): void
    {
        $user = $request->user();
        $ownsCompany = $user->ownedCompany && $user->ownedCompany->id === $branch->company_id;
        $managesBranch = $branch->manager_user_id === $user->id;

        abort_unless($ownsCompany || $managesBranch, 403);
    }
}
