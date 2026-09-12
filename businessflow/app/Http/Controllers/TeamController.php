<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Support\Modules;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    /**
     * Roles a business's own Team page can never edit/remove — the
     * business's own Owner, plus anyone with cross-business access from
     * above (Company Owner / Branch Manager, see App\Models\Company /
     * Branch) who was auto-attached when this builder was created.
     */
    private const PROTECTED_ROLES = ['owner', 'company_owner', 'branch_manager'];

    public function index(): View
    {
        $business = Business::findOrFail(Tenant::id());
        $members = $business->users()->orderBy('business_user.created_at')->get();

        return view('team.index', compact('members'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'modules' => ['array'],
            'modules.*' => ['string', 'in:'.implode(',', array_keys(Modules::ALL))],
            'financials' => ['array'],
            'financials.*' => ['string', 'in:'.implode(',', Modules::FINANCIAL_MODULES)],
        ]);

        $business = Business::findOrFail(Tenant::id());
        $user = User::where('email', $data['email'])->first();

        if ($user && $business->users()->where('users.id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'This person is already on your team.'])->withInput();
        }

        if (! $user) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);
        }

        $business->users()->attach($user->id, [
            'role' => 'supervisor',
            'permissions' => [
                'modules' => $data['modules'] ?? [],
                'financials' => array_values(array_intersect($data['financials'] ?? [], $data['modules'] ?? [])),
            ],
            'status' => 'active',
        ]);

        return redirect()->route('team.index')->with('status', 'Team member added.');
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't change this member's permissions here.");

        $data = $request->validate([
            'modules' => ['array'],
            'modules.*' => ['string', 'in:'.implode(',', array_keys(Modules::ALL))],
            'financials' => ['array'],
            'financials.*' => ['string', 'in:'.implode(',', Modules::FINANCIAL_MODULES)],
        ]);

        $business->users()->updateExistingPivot($member->id, [
            'permissions' => [
                'modules' => $data['modules'] ?? [],
                'financials' => array_values(array_intersect($data['financials'] ?? [], $data['modules'] ?? [])),
            ],
        ]);

        return back()->with('status', 'Permissions updated.');
    }

    public function destroy(User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't remove this member here.");

        $business->users()->detach($member->id);

        return back()->with('status', 'Team member removed.');
    }

    /**
     * Suspending sets business_user.status, which IdentifyTenant checks
     * on every request — a suspended member is immediately signed out
     * of this business (any other business they belong to is
     * unaffected), reversible any time by switching it back to active.
     */
    public function setStatus(Request $request, User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't change this member's status here.");

        $data = $request->validate(['status' => ['required', 'in:active,suspended']]);

        $business->users()->updateExistingPivot($member->id, ['status' => $data['status']]);

        return back()->with('status', "{$member->name} is now {$data['status']}.");
    }

    /**
     * Sets a brand new password for a team member's login — there is no
     * way to recover their old one (stored hashed, one-way), so this is
     * how you help someone who's locked out: pick a new password here
     * and relay it to them yourself.
     */
    public function resetPassword(Request $request, User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if(in_array($membership->pivot->role, self::PROTECTED_ROLES, true), 422, "Can't reset this member's password here.");

        $data = $request->validate(['password' => ['required', 'string', 'min:8']]);

        $member->update(['password' => bcrypt($data['password'])]);

        return back()->with('status', "New password set for {$member->email} — tell them directly, it can't be looked up again after this.");
    }
}
