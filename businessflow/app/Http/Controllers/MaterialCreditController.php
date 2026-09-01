<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use App\Models\ProjectCost;
use Illuminate\View\View;

class MaterialCreditController extends Controller
{
    /**
     * "Udhar" — material/labor taken on credit from a vendor, not yet
     * paid. This is the one place that answers: how much do we owe in
     * total, per project, and per vendor, right now.
     */
    public function index(): View
    {
        $entries = ProjectCost::with('project')
            ->where('is_credit', true)
            ->orderByDesc('spent_on')
            ->get();

        $outstanding = $entries->whereNull('credit_settled_at')->values();
        $settled = $entries->whereNotNull('credit_settled_at')->sortByDesc('credit_settled_at')->values();

        $totalOutstanding = (float) $outstanding->sum('amount');

        $byProject = $outstanding->groupBy(fn (ProjectCost $c) => $c->project?->name ?? 'Unknown')
            ->map(fn ($group) => (float) $group->sum('amount'))
            ->sortDesc();

        $byVendor = $outstanding->groupBy(fn (ProjectCost $c) => $c->vendor ?: 'Not specified')
            ->map(fn ($group) => (float) $group->sum('amount'))
            ->sortDesc();

        $paymentAccounts = PaymentAccount::orderBy('name')->get();

        return view('material-credit.index', compact(
            'outstanding', 'settled', 'totalOutstanding', 'byProject', 'byVendor', 'paymentAccounts'
        ));
    }
}
