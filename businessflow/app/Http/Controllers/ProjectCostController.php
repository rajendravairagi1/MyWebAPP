<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProjectCostController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'in:land,construction,material,labor,approval,marketing,other'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'spent_on' => ['required', 'date'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $project->costs()->create($data);

        return back()->with('status', 'Cost entry added.');
    }

    public function destroy(Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->id, 404);

        $cost->delete();

        return back()->with('status', 'Cost entry removed.');
    }
}
