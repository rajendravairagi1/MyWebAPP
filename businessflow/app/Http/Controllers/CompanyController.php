<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->ownedCompany) {
            return redirect()->route('company.show');
        }

        abort_unless($request->user()->hasCompanyPlan(), 403, "Your account isn't on the Company plan yet — contact us to upgrade.");

        return view('company.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->ownedCompany, 422, 'You already have a company.');
        abort_unless($request->user()->hasCompanyPlan(), 403, "Your account isn't on the Company plan yet — contact us to upgrade.");

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $company = Company::create([
            'owner_user_id' => $request->user()->id,
            'name' => $data['name'],
        ]);

        return redirect()->route('company.show')->with('status', "\"{$company->name}\" created — now add your branches.");
    }

    public function show(Request $request): View|RedirectResponse
    {
        $company = $request->user()->ownedCompany;

        if (! $company) {
            return redirect()->route('company.create');
        }

        $branches = $company->branches()->with(['manager', 'businesses'])->orderBy('created_at')->get();

        $branchStats = $branches->mapWithKeys(function ($branch) {
            $totals = ['projects' => 0, 'customers' => 0, 'value' => 0, 'collected' => 0, 'outstanding' => 0, 'cost' => 0, 'profit' => 0];

            foreach ($branch->businesses as $business) {
                foreach ($business->statsSummary() as $key => $value) {
                    $totals[$key] += $value;
                }
            }

            return [$branch->id => $totals];
        });

        $companyTotals = ['projects' => 0, 'customers' => 0, 'value' => 0, 'collected' => 0, 'outstanding' => 0, 'cost' => 0, 'profit' => 0];
        foreach ($branchStats as $totals) {
            foreach ($totals as $key => $value) {
                $companyTotals[$key] += $value;
            }
        }

        // Company-wide totals mix every branch's builders together, so
        // there's no single "correct" currency if they ever differ — in
        // practice a company operates in one country/currency, so the
        // first builder found stands in for the whole company view.
        $companyCurrencySymbol = \App\Models\Business::symbolFor($branches->flatMap->businesses->first()?->currency);

        return view('company.show', compact('company', 'branches', 'branchStats', 'companyTotals', 'companyCurrencySymbol'));
    }
}
