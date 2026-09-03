<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

/**
 * The platform owner's panel (see App\Http\Middleware\EnsurePlatformAdmin)
 * — the only place any customer account or plan tier gets created today.
 * Payment is collected manually (UPI/bank transfer) outside the app; this
 * is just where you provision what they paid for.
 */
class AdminController extends Controller
{
    /**
     * Same cache-clearing MigrateController already does after every
     * deploy (view/config/route cache + opcache) — as a one-click button
     * here for whenever nothing changed except code that's rendering
     * stale (no host terminal/SSH on this plan, so /migrate?token=... was
     * the only other way to trigger this).
     */
    public function clearCache(): RedirectResponse
    {
        foreach (['view:clear', 'config:clear', 'route:clear', 'cache:clear'] as $command) {
            try {
                Artisan::call($command);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return back()->with('status', __('Cache cleared — the site is now running the latest deployed code.'));
    }

    public function index(): View
    {
        $businesses = \App\Models\Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->orderBy('created_at')
            ->get();

        $companies = Company::with('owner')->withCount('branches')->orderBy('created_at')->get();

        // Normally exactly one row — but if "is_demo" ever gets ticked by
        // mistake on a real customer account, that account silently
        // disappears from $businesses above (and from every other admin
        // list), since it's tenant-scoped like everything else. Fetching
        // every is_demo=true row here (not just the first) is what
        // surfaces that mistake so it can be undone from the page below,
        // instead of a customer's whole account effectively vanishing.
        $demoBusinesses = \App\Models\Business::where('is_demo', true)
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->orderBy('created_at')
            ->get();

        return view('admin.index', compact('businesses', 'companies', 'demoBusinesses'));
    }

    public function create(): View
    {
        return view('admin.create', [
            'businessTypes' => config('business.types'),
            'currencies' => config('business.currencies'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan' => ['required', 'in:solo,team,company'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255'],
            'owner_password' => ['required', 'string', 'min:8'],
            'account_name' => ['required', 'string', 'max:255'],
            'business_type' => ['required_unless:plan,company', 'nullable', 'string', 'in:'.implode(',', array_keys(config('business.types')))],
            'country' => ['required_unless:plan,company', 'nullable', 'string', 'max:2'],
            'currency' => ['required_unless:plan,company', 'nullable', 'string', 'size:3'],
            'timezone' => ['required_unless:plan,company', 'nullable', 'string', 'timezone'],
            'is_demo' => ['nullable', 'boolean'],
            'subscription_expires_at' => ['nullable', 'date'],
        ]);

        if (User::where('email', $data['owner_email'])->exists()) {
            return back()->withErrors(['owner_email' => 'A user with this email already exists.'])->withInput();
        }

        // Only one account can ever be "the" public demo — a second one
        // ticked by mistake used to silently vanish from every admin list
        // (see index()) since it's excluded the same way the real demo
        // is meant to be excluded. Catching it here means a real
        // customer account never disappears that way again.
        if ($request->boolean('is_demo') && \App\Models\Business::where('is_demo', true)->exists()) {
            return back()->withErrors(['is_demo' => 'A public demo account already exists. Leave "This is the public demo account" unticked for a real customer.'])->withInput();
        }

        $user = User::create([
            'name' => $data['owner_name'],
            'email' => $data['owner_email'],
            'password' => bcrypt($data['owner_password']),
        ]);

        if ($data['plan'] === 'company') {
            Company::create([
                'owner_user_id' => $user->id,
                'name' => $data['account_name'],
                'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            ]);

            return redirect()->route('admin.index')->with('status', "Company account \"{$data['account_name']}\" created for {$user->email}.");
        }

        $user->businesses()->create([
            'name' => $data['account_name'],
            'business_type' => $data['business_type'],
            'country' => $data['country'],
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'invoice_prefix' => 'INV',
            'plan' => $data['plan'],
            'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            'is_demo' => $request->boolean('is_demo'),
        ], [
            'role' => 'owner',
            'status' => 'active',
        ]);

        return redirect()->route('admin.index')->with('status', "Account \"{$data['account_name']}\" created for {$user->email}.");
    }

    public function updatePlan(Request $request, \App\Models\Business $business): RedirectResponse
    {
        abort_if($business->branch_id, 422, "This builder's plan is set by its Company, not per-builder.");

        $data = $request->validate([
            'plan' => ['required', 'in:solo,team,company'],
        ]);

        $business->update(['plan' => $data['plan']]);

        return back()->with('status', "\"{$business->name}\" is now on the {$data['plan']} plan.");
    }

    /**
     * Payment is collected manually outside the app, so this is how you
     * record when a business paid through to — access is cut off the day
     * after (see App\Http\Middleware\EnsureSubscriptionActive).
     */
    public function updateExpiry(Request $request, \App\Models\Business $business): RedirectResponse
    {
        abort_if($business->branch_id, 422, "This builder's billing is set on its Company, not per-builder.");

        $data = $request->validate([
            'subscription_expires_at' => ['nullable', 'date'],
        ]);

        $business->update([
            'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            // A new date means a new renewal cycle — never let a stale
            // dismissal hide the next one.
            'renewal_alert_dismissed_at' => null,
        ]);

        return back()->with('status', $data['subscription_expires_at']
            ? "\"{$business->name}\" is now valid through {$data['subscription_expires_at']}."
            : "\"{$business->name}\" has no expiry set (won't be locked out).");
    }

    /**
     * Sets a brand new password for a customer's login — there is no
     * way to recover their old one (it's stored hashed, one-way, same
     * as everywhere else in this app), so this is how you help someone
     * who's locked out: pick a new password here and relay it to them
     * yourself (call/WhatsApp/email) the same way you handed it out
     * when the account was first created.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update(['password' => bcrypt($data['password'])]);

        return back()->with('status', "New password set for {$user->email} — tell them the new password directly, it can't be looked up again after this.");
    }

    public function updateCompanyExpiry(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'subscription_expires_at' => ['nullable', 'date'],
        ]);

        $company->update([
            'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            'renewal_alert_dismissed_at' => null,
        ]);

        return back()->with('status', $data['subscription_expires_at']
            ? "\"{$company->name}\" is now valid through {$data['subscription_expires_at']}."
            : "\"{$company->name}\" has no expiry set (won't be locked out).");
    }

    /**
     * "Done" on a renewal nudge — hides it from the bell/Expiring Soon
     * page until subscription_expires_at is changed again (see above).
     */
    public function dismissBusinessRenewal(\App\Models\Business $business): RedirectResponse
    {
        $business->update(['renewal_alert_dismissed_at' => now()]);

        return back()->with('status', "Renewal reminder for \"{$business->name}\" dismissed for this cycle.");
    }

    public function dismissCompanyRenewal(Company $company): RedirectResponse
    {
        $company->update(['renewal_alert_dismissed_at' => now()]);

        return back()->with('status', "Renewal reminder for \"{$company->name}\" dismissed for this cycle.");
    }

    /**
     * Dedicated page (separate from the bell) listing every builder/solo/
     * company account whose expiry is within 7 days or already past.
     */
    public function expiringSoon(): View
    {
        return view('admin.expiring', [
            'alerts' => \App\Support\RenewalAlerts::all(),
        ]);
    }

    /**
     * Wipes the demo business's data so the next prospect starts clean —
     * same deletion set as the existing per-business Reset Data feature,
     * just triggered by you instead of the account owner via a token URL.
     *
     * Takes the specific business explicitly (from the button on its own
     * card) rather than "whichever one happens to be is_demo=true first"
     * — with more than one is_demo row (see index()), that ambiguity
     * used to risk wiping a real customer's data by mistake.
     */
    public function resetDemo(\App\Models\Business $business): RedirectResponse
    {
        abort_unless($business->is_demo, 404, 'This account is not marked as the demo account.');

        Tenant::runAs($business->id, function () {
            \Illuminate\Support\Facades\DB::transaction(function () {
                \App\Models\CustomerDocument::each(fn ($d) => \Illuminate\Support\Facades\Storage::disk('local')->delete($d->path));
                \App\Models\Invoice::query()->delete();
                \App\Models\Quotation::query()->delete();
                \App\Models\Followup::query()->delete();
                \App\Models\CustomerDocument::query()->delete();
                \App\Models\Customer::query()->delete();
                \App\Models\Project::query()->delete();
                \App\Models\Product::query()->delete();
            });
        });

        return back()->with('status', 'Demo account data reset.');
    }

    /**
     * Un-ticks "is_demo" on an account that was mistakenly marked as the
     * public demo — restoring it to a normal customer account so it shows
     * up again in the Businesses list, gets its own plan/expiry managed
     * normally, and is no longer shared with anyone who clicks the
     * homepage "See Demo" button.
     */
    public function unmarkDemo(\App\Models\Business $business): RedirectResponse
    {
        $business->update(['is_demo' => false]);

        return back()->with('status', "\"{$business->name}\" is now a normal customer account again.");
    }
}
