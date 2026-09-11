<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Customer;
use App\Models\PaymentAccount;
use App\Models\Project;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        // A project with every unit sold & paid off (or written off) is
        // done — it clutters this active list, and stays reachable via the
        // Completed Projects page or a direct link. Checked off the units
        // themselves (not the project's own status flag), since that flag
        // can go stale on older data.
        $query = Project::withCount('units')
            ->where(function ($q) {
                $q->doesntHave('units')
                    ->orWhereHas('units', fn ($qq) => $qq->whereNull('archived_at'));
            })
            ->when($request->string('q')->trim()->isNotEmpty(), fn ($q) => $q->where('name', 'like', '%'.$request->string('q')->trim().'%'));

        // Portfolio totals must reflect every matching project, not just
        // the current page — computed here before paginating the list.
        $allMatching = (clone $query)->get();
        $ongoingCount = $allMatching->where('status', 'ongoing')->count();
        $totalCost = $allMatching->sum(fn ($p) => $p->totalCost());
        $totalRevenue = $allMatching->sum(fn ($p) => $p->totalRevenue());
        $totalProfit = $totalRevenue - $totalCost;

        $projects = $query->latest()
            ->paginate(\App\Support\ListPagination::perPage($request))
            ->withQueryString();

        return view('projects.index', compact('projects', 'ongoingCount', 'totalCost', 'totalRevenue', 'totalProfit'));
    }

    public function create(): View
    {
        return view('projects.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $project = Project::create($this->validated($request));

        return redirect()->route('projects.show', $project)->with('status', 'Project created.');
    }

    public function show(Project $project): View
    {
        $project->load([
            'costs' => fn ($q) => $q->latest('spent_on'),
            'costs.account',
            'costs.contractor',
            'costs.workOrder',
            'workOrders.contractor',
            // A unit that's fully paid off or written off is done — it
            // no longer needs managing here, and it's already visible via
            // the customer's History or the Completed Projects page.
            'units' => fn ($q) => $q->whereNull('archived_at')->orderBy('unit_number'),
            'units.materialEntries',
            'units.broker',
            'quotations' => fn ($q) => $q->latest(),
            'invoices' => fn ($q) => $q->latest(),
        ]);

        $costsByCategory = $project->costs->groupBy('category')->map(fn ($group) => $group->sum('amount'));

        $customers = Customer::orderBy('name')->get();

        $paymentAccounts = PaymentAccount::orderBy('name')->get();

        $contractors = Tenant::can('contractors') ? Contractor::orderBy('name')->get() : collect();

        return view('projects.show', compact('project', 'costsByCategory', 'customers', 'paymentAccounts', 'contractors'));
    }

    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $project->update($this->validated($request));

        return redirect()->route('projects.show', $project)->with('status', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        foreach ($project->costs as $cost) {
            if ($cost->bill_path) {
                Storage::disk('local')->delete($cost->bill_path);
            }
        }

        $project->delete();

        return redirect()->route('projects.index')->with('status', 'Project deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:residential,commercial,plot,mixed'],
            'location' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'in:planning,ongoing,completed,on_hold'],
            'start_date' => ['nullable', 'date'],
            'expected_completion_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
