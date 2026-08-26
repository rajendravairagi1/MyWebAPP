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

        return view('company.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->ownedCompany, 422, 'You already have a company.');

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
            $totals = ['projects' => 0, 'customers' => 0, 'value' => 0, 'collected' => 0, 'outstanding' => 0];

            foreach ($branch->businesses as $business) {
                foreach ($business->statsSummary() as $key => $value) {
                    $totals[$key] += $value;
                }
            }

            return [$branch->id => $totals];
        });

        return view('company.show', compact('company', 'branches', 'branchStats'));
    }
}
