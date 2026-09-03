<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\PropertyDeal;
use App\Rules\Phone;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailablePropertiesController extends Controller
{
    public function index(): View
    {
        $units = ProjectUnit::with(['project', 'photos'])
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->get()
            ->sortBy([['project.name', 'asc'], ['unit_number', 'asc']]);

        $projects = Project::orderBy('name')->get(['id', 'name']);

        $deals = collect();
        if (Tenant::can('property_deals')) {
            $deals = PropertyDeal::with('photos')
                ->where('status', 'open')
                ->orderBy('property_title')
                ->get();
        }

        return view('available-properties.index', compact('units', 'projects', 'deals'));
    }

    /**
     * Quick-add a property straight from this page — either onto an
     * existing project, or by spinning up a minimal standalone project on
     * the spot (e.g. a single plot that isn't part of a bigger project),
     * so a one-off sale doesn't need a full project set up first.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'new_project_name' => ['required_without:project_id', 'nullable', 'string', 'max:255'],
            'new_project_type' => ['required_without:project_id', 'nullable', 'in:residential,commercial,plot,mixed'],
            'unit_number' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:100'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:30', new Phone],
            'contact_email' => ['nullable', 'email', 'max:255'],
        ]);

        $project = ! empty($data['project_id'])
            ? Project::findOrFail($data['project_id'])
            : Project::create([
                'name' => $data['new_project_name'],
                'type' => $data['new_project_type'],
                'status' => 'ongoing',
            ]);

        $unit = $project->units()->create([
            'unit_number' => $data['unit_number'],
            'type' => $data['type'] ?? null,
            'area_sqft' => $data['area_sqft'] ?? null,
            'price' => $data['price'],
            'status' => 'available',
            'contact_name' => $data['contact_name'] ?? null,
            'contact_phone' => $data['contact_phone'] ?? null,
            'contact_email' => $data['contact_email'] ?? null,
        ]);

        $project->syncCompletionStatus();

        return redirect()->route('project-units.show', $unit)->with('status', 'Property added — you can now upload photos, layout and papers.');
    }
}
