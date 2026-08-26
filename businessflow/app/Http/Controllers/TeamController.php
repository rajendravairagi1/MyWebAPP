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
            'permissions' => ['modules' => $data['modules'] ?? []],
            'status' => 'active',
        ]);

        return redirect()->route('team.index')->with('status', 'Team member added.');
    }

    public function update(Request $request, User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if($membership->pivot->role === 'owner', 422, "Can't change the owner's permissions.");

        $data = $request->validate([
            'modules' => ['array'],
            'modules.*' => ['string', 'in:'.implode(',', array_keys(Modules::ALL))],
        ]);

        $business->users()->updateExistingPivot($member->id, [
            'permissions' => ['modules' => $data['modules'] ?? []],
        ]);

        return back()->with('status', 'Permissions updated.');
    }

    public function destroy(User $member): RedirectResponse
    {
        $business = Business::findOrFail(Tenant::id());
        $membership = $business->users()->where('users.id', $member->id)->first();
        abort_unless($membership, 404);
        abort_if($membership->pivot->role === 'owner', 422, "Can't remove the owner.");

        $business->users()->detach($member->id);

        return back()->with('status', 'Team member removed.');
    }
}
