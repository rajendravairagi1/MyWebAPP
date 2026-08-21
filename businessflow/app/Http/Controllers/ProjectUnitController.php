<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectUnitController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'unit_number' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:100'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,booked,sold'],
        ]);

        $project->units()->create($data);

        return back()->with('status', 'Unit added.');
    }

    public function update(Request $request, Project $project, ProjectUnit $unit): RedirectResponse
    {
        abort_unless($unit->project_id === $project->id, 404);

        $data = $request->validate([
            'unit_number' => ['required', 'string', 'max:100'],
            'type' => ['nullable', 'string', 'max:100'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,booked,sold'],
        ]);

        $unit->update($data);

        return back()->with('status', 'Unit updated.');
    }

    public function destroy(Project $project, ProjectUnit $unit): RedirectResponse
    {
        abort_unless($unit->project_id === $project->id, 404);

        $unit->delete();

        return back()->with('status', 'Unit removed.');
    }
}
