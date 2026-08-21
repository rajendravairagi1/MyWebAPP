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
            'category_other' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'spent_on' => ['required', 'date'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['category'] === 'other' && filled($data['category_other'] ?? null)) {
            $data['category'] = $data['category_other'];
        }
        unset($data['category_other']);

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
