<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LedgerEntry;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectUnit;
use App\Models\PropertyDeal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function index(): View
    {
        // Only booked/sold units represent a committed sale — an
        // "available" unit isn't revenue yet. ->withTrashed() on the
        // customer relation keeps a deleted customer's name/history
        // showing here rather than silently disappearing — the sale
        // still happened.
        $soldUnits = ProjectUnit::with(['project', 'customer' => fn ($q) => $q->withTrashed()])
            ->whereIn('status', ['booked', 'sold'])
            ->get();

        $manualIncome = (float) LedgerEntry::where('type', 'income')->sum('amount');
        $manualExpense = (float) LedgerEntry::where('type', 'expense')->sum('amount');
        // Resale/trading deals aren't tied to a project (see
        // PropertyDeal), so their margin is folded in here as its own
        // term rather than mixed into Purchases/collected-from-units.
        $dealsProfit = (float) PropertyDeal::where('status', 'sold')->get()->sum(fn (PropertyDeal $d) => $d->profit());

        $totalSaleValue = (float) $soldUnits->sum('price');
        $totalCollected = (float) $soldUnits->sum(fn ($u) => $u->totalCollected());
        $totalOutstanding = (float) $soldUnits->sum(fn ($u) => $u->totalOutstanding());
        $totalPurchases = (float) ProjectCost::sum('amount') + $manualExpense;
        // Profit is on money actually in hand, not the full booked sale
        // value — an unpaid/outstanding sale isn't profit until it's
        // collected, and a written-off balance never will be.
        $netProfit = $totalCollected + $manualIncome + $dealsProfit - $totalPurchases;

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

        $entries = LedgerEntry::with(['customer' => fn ($q) => $q->withTrashed(), 'project'])->orderByDesc('entry_date')->orderByDesc('id')->limit(100)->get();

        $customers = Customer::orderBy('name')->get();
        $allProjects = Project::orderBy('name')->get();

        $deals = PropertyDeal::orderByDesc('deal_date')->orderByDesc('id')->get();

        return view('ledger.index', compact(
            'totalSaleValue', 'totalCollected', 'totalOutstanding', 'totalPurchases', 'netProfit',
            'manualIncome', 'manualExpense', 'dealsProfit', 'deals', 'projects', 'customerRows', 'entries', 'customers', 'allProjects'
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
