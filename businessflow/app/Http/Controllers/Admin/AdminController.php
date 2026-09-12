<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\SignupRequest;
use App\Models\User;
use App\Support\RenewalAlerts;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
        $businesses = Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->orderBy('created_at')
            ->get();

        $companies = Company::with('owner')->withCount('branches')->orderBy('created_at')->get();

        // "Remove" never deletes — it archives (soft delete). The
        // archived list itself lives on its own page (see archived()
        // below) since it can grow large — this is just the count for
        // the header button/badge.
        $archivedCount = Business::onlyTrashed()->whereNull('branch_id')->count()
            + Company::onlyTrashed()->count();

        // Normally exactly one row — but if "is_demo" ever gets ticked by
        // mistake on a real customer account, that account silently
        // disappears from $businesses above (and from every other admin
        // list), since it's tenant-scoped like everything else. Fetching
        // every is_demo=true row here (not just the first) is what
        // surfaces that mistake so it can be undone from the page below,
        // instead of a customer's whole account effectively vanishing.
        $demoBusinesses = Business::where('is_demo', true)
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->orderBy('created_at')
            ->get();

        $settings = PlatformSetting::current();

        $pendingSignupRequestsCount = SignupRequest::where('status', 'pending')->count();

        return view('admin.index', compact('businesses', 'companies', 'demoBusinesses', 'archivedCount', 'settings', 'pendingSignupRequestsCount'));
    }

    /**
     * Archived (soft-deleted) accounts, on their own page since "Remove"
     * accumulates over time and doesn't belong inline on the main list.
     * Businesses and Companies are two different models, so they're
     * merged into one plain collection here and paginated manually
     * (LengthAwarePaginator over an in-memory slice) rather than via a
     * SQL UNION, which would need to reconcile two very different
     * column sets (and, for Business, the branch_id-scoped exclusion).
     * Fine at the scale an admin-only archive list actually reaches.
     */
    public function archived(Request $request): View
    {
        $perPage = \App\Support\ListPagination::perPage($request);
        $search = $request->string('q')->trim()->toString();

        $businesses = Business::onlyTrashed()
            ->whereNull('branch_id')
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->get()
            ->map(fn (Business $business) => (object) [
                'type' => 'business',
                'id' => $business->id,
                'name' => $business->name,
                'owner_name' => $business->users->first()?->name,
                'owner_email' => $business->users->first()?->email,
                'phone' => $business->phone,
                'deleted_at' => $business->deleted_at,
            ]);

        $companies = Company::onlyTrashed()
            ->with('owner')
            ->when($search, fn ($q) => $q->where('name', 'like', '%'.$search.'%'))
            ->get()
            ->map(fn (Company $company) => (object) [
                'type' => 'company',
                'id' => $company->id,
                'name' => $company->name,
                'owner_name' => $company->owner->name,
                'owner_email' => $company->owner->email,
                'phone' => $company->phone,
                'deleted_at' => $company->deleted_at,
            ]);

        $merged = $businesses->concat($companies)->sortByDesc('deleted_at')->values();

        $page = (int) $request->query('page', 1);
        $items = $merged->forPage($page, $perPage)->values();

        $archived = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $merged->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.archived', ['archived' => $archived]);
    }

    /**
     * The footer credit line and support WhatsApp number shown across
     * the whole product — platform-wide, not tied to any one business,
     * so they live here rather than in a customer's own Business
     * Settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'footer_text' => ['nullable', 'string', 'max:255'],
            'support_whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
        ]);

        PlatformSetting::current()->update($data);

        return back()->with('status', 'Platform settings updated.');
    }

    public function create(Request $request): View
    {
        $signupRequest = null;

        if ($request->filled('signup_request_id')) {
            $signupRequest = SignupRequest::where('status', 'pending')->find($request->integer('signup_request_id'));
        }

        return view('admin.create', [
            'businessTypes' => config('business.types'),
            'currencies' => config('business.currencies'),
            'signupRequest' => $signupRequest,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $signupRequest = null;
        if ($request->filled('signup_request_id')) {
            // Still pending — abort_unless rather than a plain 404, so a
            // request approved twice from two open tabs gets a clear
            // reason instead of a confusing "not found" on the second.
            $signupRequest = SignupRequest::where('status', 'pending')->findOrFail($request->integer('signup_request_id'));
        }

        // A left-blank text input still posts as an empty string, not a
        // missing key — normalize that to a real null first so 'nullable'
        // below actually skips the 'min:8' rule instead of rejecting "".
        if (! $request->filled('owner_password')) {
            $request->merge(['owner_password' => null]);
        }

        $data = $request->validate([
            'plan' => ['required', 'in:solo,team,company'],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_email' => ['required', 'email', 'max:255'],
            // Coming from an approved signup request, the customer already
            // chose their own password (see SignupRequest::password_hash
            // below) — this field only needs a value when there's no
            // request to inherit one from.
            'owner_password' => [$signupRequest ? 'nullable' : 'required', 'string', 'min:8'],
            'account_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
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
        if ($request->boolean('is_demo') && Business::where('is_demo', true)->exists()) {
            return back()->withErrors(['is_demo' => 'A public demo account already exists. Leave "This is the public demo account" unticked for a real customer.'])->withInput();
        }

        // Already bcrypt-hashed at submission time on the public form, so
        // this is never re-hashed (User::password casts as 'hashed', which
        // leaves an already-hashed value alone) — the exact password the
        // customer chose keeps working after approval, without Rajendra
        // ever seeing or retyping it.
        $password = $signupRequest && ! ($data['owner_password'] ?? null)
            ? $signupRequest->password_hash
            : bcrypt($data['owner_password']);

        $user = User::create([
            'name' => $data['owner_name'],
            'email' => $data['owner_email'],
            'password' => $password,
        ]);

        if ($data['plan'] === 'company') {
            $company = Company::create([
                'owner_user_id' => $user->id,
                'name' => $data['account_name'],
                'phone' => $data['phone'] ?? null,
                'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            ]);

            $signupRequest?->update(['status' => 'approved', 'reviewed_at' => now()]);

            return redirect()->route('admin.index')->with('status', "Company account \"{$data['account_name']}\" created for {$user->email}.");
        }

        $business = $user->businesses()->create([
            'name' => $data['account_name'],
            'business_type' => $data['business_type'],
            'country' => $data['country'],
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'invoice_prefix' => 'INV',
            'plan' => $data['plan'],
            'subscription_expires_at' => $data['subscription_expires_at'] ?? null,
            'is_demo' => $request->boolean('is_demo'),
        ], [
            'role' => 'owner',
            'status' => 'active',
        ]);

        $signupRequest?->update(['status' => 'approved', 'business_id' => $business->id, 'reviewed_at' => now()]);

        return redirect()->route('admin.index')->with('status', "Account \"{$data['account_name']}\" created for {$user->email}.");
    }

    public function updatePlan(Request $request, Business $business): RedirectResponse
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
    public function updateExpiry(Request $request, Business $business): RedirectResponse
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
     * A Company has no self-service settings page of its own yet (unlike
     * a Business, which sets its own phone in Business Settings) — this
     * is the only place a contact number gets recorded for one, same as
     * how plan/expiry/password are already admin-managed here.
     */
    public function updateCompanyPhone(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $company->update(['phone' => $data['phone'] ?? null]);

        return back()->with('status', "\"{$company->name}\" phone number updated.");
    }

    /**
     * "Done" on a renewal nudge — hides it from the bell/Expiring Soon
     * page until subscription_expires_at is changed again (see above).
     */
    public function dismissBusinessRenewal(Business $business): RedirectResponse
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
            'alerts' => RenewalAlerts::all(),
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
    public function resetDemo(Business $business): RedirectResponse
    {
        abort_unless($business->is_demo, 404, 'This account is not marked as the demo account.');

        Tenant::runAs($business->id, function () {
            DB::transaction(function () {
                CustomerDocument::each(fn ($d) => Storage::disk('local')->delete($d->path));
                Invoice::query()->delete();
                Quotation::query()->delete();
                Followup::query()->delete();
                CustomerDocument::query()->delete();
                Customer::query()->delete();
                Project::query()->delete();
                Product::query()->delete();
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
    public function unmarkDemo(Business $business): RedirectResponse
    {
        $business->update(['is_demo' => false]);

        return back()->with('status', "\"{$business->name}\" is now a normal customer account again.");
    }

    /**
     * Sets a business to active or inactive — an inactive account is
     * blocked from logging in (see EnsureSubscriptionActive) without
     * touching its plan, expiry, or any of its data, so it can be
     * switched back on just as instantly. Takes the target state
     * explicitly (rather than blindly flipping whatever it currently is)
     * so both the Active and Inactive buttons in admin.index are plain,
     * idempotent submits.
     */
    public function setBusinessStatus(Request $request, Business $business): RedirectResponse
    {
        abort_if($business->branch_id, 422, "This builder's status is set by its Company, not per-builder.");

        $data = $request->validate(['status' => ['required', 'in:active,inactive']]);

        $business->update($data);

        return back()->with('status', "\"{$business->name}\" is now {$data['status']}.");
    }

    public function setCompanyStatus(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:active,inactive']]);

        $company->update($data);

        return back()->with('status', "\"{$company->name}\" is now {$data['status']}.");
    }

    /**
     * "Remove" — archives the account (soft delete) instead of destroying
     * it. Everything about it (data, users, history) stays exactly as it
     * was; it just drops out of the main lists above until Restore brings
     * it back from the Archived Accounts section.
     */
    public function archiveBusiness(Business $business): RedirectResponse
    {
        abort_if($business->branch_id, 422, "This builder is part of a Company — archive the Company instead.");

        $business->delete();

        return back()->with('status', "\"{$business->name}\" has been archived — restore it any time from Archived Accounts below.");
    }

    public function restoreBusiness(int $id): RedirectResponse
    {
        $business = Business::onlyTrashed()->findOrFail($id);
        $business->restore();

        return back()->with('status', "\"{$business->name}\" has been restored.");
    }

    public function archiveCompany(Company $company): RedirectResponse
    {
        $company->delete();

        return back()->with('status', "\"{$company->name}\" has been archived — restore it any time from Archived Accounts below.");
    }

    public function restoreCompany(int $id): RedirectResponse
    {
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->restore();

        return back()->with('status', "\"{$company->name}\" has been restored.");
    }
}
