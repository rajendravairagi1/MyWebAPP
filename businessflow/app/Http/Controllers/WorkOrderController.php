<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\WorkOrder;
use App\Rules\Phone;
use App\Support\ContractorResolver;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class WorkOrderController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        abort_unless(Tenant::can('contractors'), 403);

        $data = $request->validate($this->rules());
        $data = $this->applyContractor($data);

        $project->workOrders()->create($data);

        return back()->with('status', 'Work order added.');
    }

    public function update(Request $request, Project $project, WorkOrder $workOrder): RedirectResponse
    {
        abort_unless($workOrder->project_id === $project->id, 404);
        abort_unless(Tenant::can('contractors'), 403);

        $data = $request->validate($this->rules());
        $data = $this->applyContractor($data);

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
            'contractor_id' => ['nullable', 'integer', 'required_without:new_contractor_name'],
            'new_contractor_name' => ['nullable', 'string', 'max:255'],
            'new_contractor_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(Contractor::TYPES))],
            'new_contractor_type_other' => ['nullable', 'string', 'max:100'],
            'new_contractor_phone' => ['nullable', 'string', 'max:30', new Phone],
            'description' => ['required', 'string', 'max:255'],
            'area_sqft' => ['nullable', 'numeric', 'min:0'],
            'rate_per_sqft' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Same "pick an existing one, or type a new one right here" resolver
     * the Add/Edit Payment forms already use — a work order shouldn't
     * need the contractor set up separately first. The new contractor
     * lands in the Contractors / Vendors module exactly like one created
     * from there directly.
     */
    protected function applyContractor(array $data): array
    {
        $data['contractor_id'] = ContractorResolver::resolve($data);
        unset($data['new_contractor_name'], $data['new_contractor_type'], $data['new_contractor_type_other'], $data['new_contractor_phone']);

        return $data;
    }
}
