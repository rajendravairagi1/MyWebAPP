<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The platform owner's panel (see App\Http\Middleware\EnsurePlatformAdmin)
 * — the only place any customer account or plan tier gets created today.
 * Payment is collected manually (UPI/bank transfer) outside the app; this
 * is just where you provision what they paid for.
 */
class AdminController extends Controller
{
    public function index(): View
    {
        $businesses = \App\Models\Business::whereNull('branch_id')
            ->where('is_demo', false)
            ->with(['users' => fn ($q) => $q->wherePivot('role', 'owner')])
            ->orderBy('created_at')
            ->get();

        $companies = Company::with('owner')->withCount('branches')->orderBy('created_at')->get();

        $demoBusiness = \App\Models\Business::where('is_demo', true)->first();

        return view('admin.index', compact('businesses', 'companies', 'demoBusiness'));
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
        ]);

        if (User::where('email', $data['owner_email'])->exists()) {
            return back()->withErrors(['owner_email' => 'A user with this email already exists.'])->withInput();
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
     * Wipes the demo business's data so the next prospect starts clean —
     * same deletion set as the existing per-business Reset Data feature,
     * just triggered by you instead of the account owner via a token URL.
     */
    public function resetDemo(): RedirectResponse
    {
        $demo = \App\Models\Business::where('is_demo', true)->first();
        abort_unless($demo, 404, 'No demo account set up yet.');

        Tenant::runAs($demo->id, function () {
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
}
