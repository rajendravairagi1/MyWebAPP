<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BuilderController extends Controller
{
    /**
     * Creates a new Builder (Business) inside a Branch, and auto-grants
     * full access to the Company Owner and (if set) the Branch Manager —
     * the same mechanism the existing per-business Team feature already
     * enforces everywhere else, so nothing else needs to know this
     * builder came from a Company/Branch instead of solo onboarding.
     */
    public function store(Request $request, Branch $branch): RedirectResponse
    {
        $user = $request->user();
        $ownsCompany = $user->ownedCompany && $user->ownedCompany->id === $branch->company_id;

        // Only the Company Owner can add new builders — a Branch Manager
        // manages the builders already in their branch, but doesn't grow
        // the org chart on their own.
        abort_unless($ownsCompany, 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'string', 'in:'.implode(',', array_keys(config('business.types')))],
            'country' => ['required', 'string', 'max:2'],
            'currency' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'string', 'timezone'],
        ]);

        $business = $branch->businesses()->create($data + [
            'invoice_prefix' => 'INV',
        ]);

        $grants = [$branch->company->owner_user_id => 'company_owner'];

        if ($branch->manager_user_id && $branch->manager_user_id !== $branch->company->owner_user_id) {
            $grants[$branch->manager_user_id] = 'branch_manager';
        }

        foreach ($grants as $userId => $role) {
            $business->users()->attach($userId, [
                'role' => $role,
                'permissions' => null,
                'status' => 'active',
            ]);
        }

        return back()->with('status', "\"{$business->name}\" added.");
    }
}
