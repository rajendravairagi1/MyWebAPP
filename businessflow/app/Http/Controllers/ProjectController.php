<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::withCount('units')->latest()->get();

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
            'units' => fn ($q) => $q->orderBy('unit_number'),
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
