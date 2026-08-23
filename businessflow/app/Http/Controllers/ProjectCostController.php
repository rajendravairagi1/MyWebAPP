<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectCost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            'bill' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        if ($data['category'] === 'other' && filled($data['category_other'] ?? null)) {
            $data['category'] = $data['category_other'];
        }
        unset($data['category_other']);

        if ($request->hasFile('bill')) {
            $file = $request->file('bill');
            $data['bill_path'] = $file->store('project-bills/'.$project->id, 'local');
            $data['bill_name'] = $file->getClientOriginalName();
        }
        unset($data['bill']);

        $project->costs()->create($data);

        return back()->with('status', 'Payment added.');
    }

    public function destroy(Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->id, 404);

        if ($cost->bill_path) {
            Storage::disk('local')->delete($cost->bill_path);
        }

        $cost->delete();

        return back()->with('status', 'Cost entry removed.');
    }

    public function bill(Project $project, ProjectCost $cost): StreamedResponse
    {
        abort_unless($cost->project_id === $project->id, 404);
        abort_unless(filled($cost->bill_path), 404);

        return Storage::disk('local')->response($cost->bill_path, $cost->bill_name);
    }
}
