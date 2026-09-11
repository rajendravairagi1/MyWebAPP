<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\WorkOrder;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless(Tenant::can('contractors'), 403);

        $data = $request->validate($this->rules());

        $project->workOrders()->create($data);

        return back()->with('status', 'Work order added.');
    }

    public function update(Request $request, Project $project, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->project_id === $project->id, 404);
        abort_unless(Tenant::can('contractors'), 403);

        $data = $request->validate($this->rules());

        $workOrder->update($data);

        return back()->with('status', 'Work order updated.');
    }

    /**
     * Only safe to remove while nothing has been paid against it yet —
     * once a payment is linked, deleting the work order would silently
     * strand that payment's "what contract was this for" context.
     */
    public function destroy(Project $project, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->project_id === $project->id, 404);
        abort_if($workOrder->payments()->exists(), 422, "This work order has payments recorded against it and can't be deleted.");

        $workOrder->delete();

        return back()->with('status', 'Work order removed.');
    }

    protected function rules(): array
    {
        return [
            'contractor_id' => ['required', 'integer'],
            'description' => ['required', 'string', 'max:255'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'rate_per_sqft' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
