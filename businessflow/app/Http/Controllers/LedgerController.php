<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(): View
    {
        // Only booked/sold units represent a committed sale — an
        // "available" unit isn't revenue yet.
        $soldUnits = ProjectUnit::with(['project', 'customer'])
            ->whereIn('status', ['booked', 'sold'])
            ->get();

        $manualIncome = (float) LedgerEntry::where('type', 'income')->sum('amount');
        $manualExpense = (float) LedgerEntry::where('type', 'expense')->sum('amount');

        $totalSaleValue = (float) $soldUnits->sum('price');
        $totalCollected = (float) $soldUnits->sum(fn ($u) => $u->totalCollected());
        $totalOutstanding = (float) $soldUnits->sum(fn ($u) => $u->totalOutstanding());
        $totalPurchases = (float) ProjectCost::sum('amount') + $manualExpense;
        // Profit is on money actually in hand, not the full booked sale
        // value — an unpaid/outstanding sale isn't profit until it's
        // collected, and a written-off balance never will be.
        $netProfit = $totalCollected + $manualIncome - $totalPurchases;

        $projects = Project::withCount('units')->orderBy('name')->get()->map(function (Project $project) use ($soldUnits) {
            $units = $soldUnits->where('project_id', $project->id);

            return (object) [
                'project' => $project,
                'unitCount' => $units->count(),
                'saleValue' => $units->sum('price'),
                'collected' => $units->sum(fn ($u) => $u->totalCollected()),
                'outstanding' => $units->sum(fn ($u) => $u->totalOutstanding()),
                'purchases' => $project->totalCost(),
                'profit' => $units->sum(fn ($u) => $u->totalCollected()) - $project->totalCost(),
            ];
        })->filter(fn ($row) => $row->unitCount > 0 || $row->purchases > 0)->values();

        $customerRows = $soldUnits->sortBy(fn ($u) => $u->customer?->name)->map(fn ($unit) => (object) [
            'customer' => $unit->customer,
            'unit' => $unit,
        ]);

        $entries = LedgerEntry::with(['customer', 'project'])->orderByDesc('entry_date')->orderByDesc('id')->limit(100)->get();

        $customers = Customer::orderBy('name')->get();
        $allProjects = Project::orderBy('name')->get();

        return view('ledger.index', compact(
            'totalSaleValue', 'totalCollected', 'totalOutstanding', 'totalPurchases', 'netProfit',
            'manualIncome', 'manualExpense', 'projects', 'customerRows', 'entries', 'customers', 'allProjects'
        ));
    }

    public function storeEntry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:income,expense'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'entry_date' => ['required', 'date'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        LedgerEntry::create($data + ['recorded_by' => auth()->id()]);

        return back()->with('status', 'Ledger entry added.');
    }

    public function destroyEntry(LedgerEntry $entry): RedirectResponse
    {
        $entry->delete();

        return back()->with('status', 'Ledger entry removed.');
    }
}
