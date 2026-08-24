<?php

namespace App\Http\Controllers;

use App\Models\Customer;
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
        $project->syncCompletionStatus();

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
        $project->syncCompletionStatus();

        return back()->with('status', 'Unit updated.');
    }

    public function destroy(Project $project, ProjectUnit $unit): RedirectResponse
    {
        abort_unless($unit->project_id === $project->id, 404);

        $unit->delete();
        $project->syncCompletionStatus();

        return back()->with('status', 'Unit removed.');
    }

    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_unit_id' => ['required', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'commitment_date' => ['nullable', 'date'],
        ]);

        $unit = ProjectUnit::findOrFail($data['project_unit_id']);

        if (empty($data['customer_id'])) {
            $unit->update([
                'customer_id' => null,
                'status' => $unit->status === 'sold' ? $unit->status : 'available',
            ]);
            $unit->project?->syncCompletionStatus();

            return back()->with('status', 'Property unassigned.');
        }

        $customer = Customer::findOrFail($data['customer_id']);

        $unit->update([
            'customer_id' => $customer->id,
            'status' => $unit->status === 'available' ? 'booked' : $unit->status,
            'commitment_date' => $data['commitment_date'] ?? $unit->commitment_date,
        ]);
        $unit->project?->syncCompletionStatus();

        return back()->with('status', 'Property assigned to customer.');
    }

    public function updateCommitment(Request $request, ProjectUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'commitment_date' => ['nullable', 'date'],
            'commitment_note' => ['nullable', 'string', 'max:255'],
        ]);

        $unit->update($data);

        return back()->with('status', 'Commitment date updated.');
    }

    public function writeOff(Request $request, ProjectUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_if($unit->totalOutstanding() <= 0, 422, 'Nothing outstanding to write off.');

        $unit->writeOff($data['note'] ?? null);

        return back()->with('status', 'Remaining balance written off and moved to history.');
    }

    public function recover(ProjectUnit $unit): RedirectResponse
    {
        $unit->recover();

        return back()->with('status', 'Property moved back to active.');
    }
}
