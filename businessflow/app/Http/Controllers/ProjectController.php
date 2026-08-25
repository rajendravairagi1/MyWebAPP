<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        // A project with every unit sold & paid off (or written off) is
        // done — it clutters this active list, and stays reachable via the
        // Completed Projects page or a direct link. Checked off the units
        // themselves (not the project's own status flag), since that flag
        // can go stale on older data.
        $projects = Project::withCount('units')
            ->where(function ($query) {
                $query->doesntHave('units')
                    ->orWhereHas('units', fn ($q) => $q->whereNull('archived_at'));
            })
            ->latest()
            ->get();

        return view('projects.index', compact('projects'));
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
            // A unit that's fully paid off or written off is done — it
            // no longer needs managing here, and it's already visible via
            // the customer's History or the Completed Projects page.
            'units' => fn ($q) => $q->whereNull('archived_at')->orderBy('unit_number'),
            'units.materialEntries',
            'quotations' => fn ($q) => $q->latest(),
            'invoices' => fn ($q) => $q->latest(),
        ]);

        $costsByCategory = $project->costs->groupBy('category')->map(fn ($group) => $group->sum('amount'));

        $customers = Customer::orderBy('name')->get();

        return view('projects.show', compact('project', 'costsByCategory', 'customers'));
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
