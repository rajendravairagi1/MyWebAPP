<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Every person with a login anywhere on the platform — across every
 * customer's Business and Company/Branch — in one list, with the same
 * suspend/remove/reset-password actions a business owner already has
 * for their own Team (see TeamController), just not limited to one
 * business. Business/Company-level access itself (the whole account
 * going active/inactive) stays on the existing Platform Admin page —
 * this is one level down, for the individual people inside them.
 */
class UserAdminController extends Controller
{
    /**
     * Same roles TeamController already refuses to let a business owner
     * touch — Owner, plus anyone with cross-business access from above
     * (Company Owner / Branch Manager). Their account-level access is
     * already governed by the existing Platform Admin Business/Company
     * status toggle; suspending/removing the membership row here
     * wouldn't actually lock them out the same way, so it's kept out of
     * reach here to avoid a toggle that looks like it works but doesn't.
     */
    private const PROTECTED_ROLES = ['owner', 'company_owner', 'branch_manager'];

    public function index(): View
    {
        $businesses = Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->with('users')
            ->orderBy('created_at')
            ->get();

        $branches = Business::whereNotNull('branch_id')
            ->with(['users', 'branch.company'])
            ->orderBy('created_at')
            ->get();

        $memberships = collect();

        foreach ($businesses->concat($branches) as $business) {
            $label = $business->branch
                ? $business->branch->company->name.' — '.$business->name
                : $business->name;

            foreach ($business->users as $user) {
                $memberships->push([
                    'business_id' => $business->id,
                    'business_label' => $label,
                    'user' => $user,
                    'role' => $user->pivot->role,
                    'status' => $user->pivot->status,
                    'protected' => in_array($user->pivot->role, self::PROTECTED_ROLES, true),
                ]);
            }
        }

        $memberships = $memberships->sortBy([
            fn ($m) => $m['user']->name,
            fn ($m) => $m['business_label'],
        ])->values();

        return view('admin.users', compact('memberships'));
    }

    public function setStatus(Request $request, Business $business, User $user): RedirectResponse
    {
        $membership = $business->users()->where('users.id', $user->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't change this person's status here — use the Business/Company status toggle on Platform Admin instead.");

        $data = $request->validate(['status' => ['required', 'in:active,suspended']]);

        $business->users()->updateExistingPivot($user->id, ['status' => $data['status']]);

        return back()->with('status', "{$user->name} is now {$data['status']} on \"{$business->name}\".");
    }

    public function destroy(Business $business, User $user): RedirectResponse
    {
        $membership = $business->users()->where('users.id', $user->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't remove this person here — use the Business/Company status toggle on Platform Admin instead.");

        $business->users()->detach($user->id);

        return back()->with('status', "{$user->name} removed from \"{$business->name}\".");
    }
}
