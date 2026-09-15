<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\WorkOrder;
use App\Rules\Phone;
use App\Support\ContractorResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectCostController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate($this->rules($request, isCreate: true));

        $data = $this->applyPaymentType($request, $project, $data);

        if ($request->hasFile('bill')) {
            $file = $request->file('bill');
            $data['bill_path'] = $file->store('project-bills/'.$project->id, 'local');
            $data['bill_name'] = $file->getClientOriginalName();
        }
        unset($data['bill']);

        $project->costs()->create($data);

        return back()->with('status', ($data['is_credit'] ?? false) ? 'Payment added — marked as Udhar (unpaid), showing on the Material Udhar page.' : 'Payment added.');
    }

    public function update(Request $request, Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->id, 404);

        $data = $request->validate($this->rules($request, isCreate: false));

        $data = $this->applyPaymentType($request, $project, $data);

        if ($request->hasFile('bill')) {
            if ($cost->bill_path) {
                Storage::disk('local')->delete($cost->bill_path);
            }
            $file = $request->file('bill');
            $data['bill_path'] = $file->store('project-bills/'.$project->id, 'local');
            $data['bill_name'] = $file->getClientOriginalName();
        }
        unset($data['bill']);

        $cost->update($data);

        return back()->with('status', 'Payment updated.');
    }

    /**
     * Settle an outstanding "udhar" — the money actually leaves an
     * account only now, on this date, not on the original spend date.
     */
    public function settle(Request $request, Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->id, 404);
        abort_unless($cost->isOutstandingCredit(), 422, 'This entry is not an outstanding credit purchase.');

        $data = $request->validate([
            'payment_account_id' => ['required', 'integer'],
            'credit_settled_at' => ['required', 'date'],
        ]);

        $cost->update($data);

        return back()->with('status', 'Udhar marked as paid.');
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

    /**
     * The Add/Edit Payment form is split into two paths that used to be
     * one confusing form mixing both together — see ProjectCost's own
     * doc comment. 'contractor': paying a contractor against a Work
     * Order (no category picker, no vendor, no credit/udhar — a
     * contractor's "how much is still owed" already comes from the
     * Work Order's own balance()). 'material': a Land/Construction/
     * Material/etc. expense, optionally to a vendor, optionally on
     * credit (shows on the Material Udhar page).
     *
     * A brand new contractor payment must pick or create a Work Order,
     * so its contract value is always captured — but editing an entry
     * that predates this rule (work order was optional before) doesn't
     * force one retroactively.
     */
    protected function rules(Request $request, bool $isCreate): array
    {
        $common = [
            'payment_type' => ['required', 'in:contractor,material'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'spent_on' => ['required', 'date'],
            'payment_account_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'bill' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ];

        if ($request->input('payment_type') === 'contractor') {
            return $common + [
                'description' => ['nullable', 'string', 'max:255'],
                'contractor_id' => ['nullable', 'integer', 'required_without:new_contractor_name'],
                'new_contractor_name' => ['nullable', 'string', 'max:255'],
                'new_contractor_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(Contractor::TYPES))],
                'new_contractor_type_other' => ['nullable', 'string', 'max:100'],
                'new_contractor_phone' => ['nullable', 'string', 'max:30', new Phone],
                'work_order_mode' => $isCreate ? ['required', 'in:existing,new'] : ['nullable', 'in:existing,new'],
                'work_order_id' => ['nullable', 'integer', 'required_if:work_order_mode,existing'],
                'new_work_order_description' => ['nullable', 'string', 'max:255', 'required_if:work_order_mode,new'],
                'new_work_order_area_sqft' => ['nullable', 'numeric', 'min:0'],
                'new_work_order_rate_per_sqft' => ['nullable', 'numeric', 'min:0'],
                'new_work_order_total_amount' => ['nullable', 'numeric', 'min:0.01', 'required_if:work_order_mode,new'],
            ];
        }

        return $common + [
            'description' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:land,construction,material,labor,approval,marketing,other'],
            'category_other' => ['nullable', 'string', 'max:100'],
            'vendor_contractor_id' => ['nullable', 'integer'],
            'new_vendor_name' => ['nullable', 'string', 'max:255'],
            'new_vendor_phone' => ['nullable', 'string', 'max:30', new Phone],
            'is_credit' => ['nullable', 'boolean'],
        ];
    }

    protected function applyPaymentType(Request $request, Project $project, array $data): array
    {
        return $data['payment_type'] === 'contractor'
            ? $this->applyContractorPayment($project, $data)
            : $this->applyMaterialPayment($data);
    }

    protected function applyContractorPayment(Project $project, array $data): array
    {
        $data['contractor_id'] = ContractorResolver::resolve($data);
        unset($data['new_contractor_name'], $data['new_contractor_type'], $data['new_contractor_type_other'], $data['new_contractor_phone']);

        if (($data['work_order_mode'] ?? null) === 'new' && filled($data['new_work_order_description'] ?? null)) {
            $workOrder = $project->workOrders()->create([
                'contractor_id' => $data['contractor_id'],
                'description' => $data['new_work_order_description'],
                'area_sqft' => $data['new_work_order_area_sqft'] ?? null,
                'rate_per_sqft' => $data['new_work_order_rate_per_sqft'] ?? null,
                'total_amount' => $data['new_work_order_total_amount'],
            ]);
            $data['work_order_id'] = $workOrder->id;
        } elseif (filled($data['work_order_id'] ?? null)) {
            // Only keep it if it actually belongs to this project + contractor
            // — same guard as before, e.g. the contractor got switched after
            // picking a work order.
            $matches = WorkOrder::where('id', $data['work_order_id'])
                ->where('project_id', $project->id)
                ->where('contractor_id', $data['contractor_id'])
                ->exists();
            $data['work_order_id'] = $matches ? $data['work_order_id'] : null;
        } else {
            $data['work_order_id'] = null;
        }

        unset($data['work_order_mode'], $data['new_work_order_description'], $data['new_work_order_area_sqft'], $data['new_work_order_rate_per_sqft'], $data['new_work_order_total_amount']);

        // Category and (if left blank) description are implied by who's
        // being paid — a contractor payment doesn't ask for either.
        $contractor = $data['contractor_id'] ? Contractor::find($data['contractor_id']) : null;
        $data['category'] = $contractor?->defaultProjectCostCategory() ?? 'construction';
        if (blank($data['description'] ?? null)) {
            $data['description'] = $contractor ? __('Payment to :name', ['name' => $contractor->name]) : __('Contractor payment');
        }

        return $data;
    }

    protected function applyMaterialPayment(array $data): array
    {
        if (($data['category'] ?? null) === 'other' && filled($data['category_other'] ?? null)) {
            $data['category'] = $data['category_other'];
        }
        unset($data['category_other']);

        // Distinct field names from the Contractor tab (vendor_contractor_id,
        // new_vendor_*) even though both ultimately resolve to the same
        // contractor_id column — the add/edit forms render both tabs' inputs
        // in the DOM at once (just one hidden via x-show), and two inputs
        // sharing a name would collide on submit otherwise. A vendor is
        // always resolved as type 'vendor' — the form doesn't ask, it's
        // already implied by being on this tab.
        $data['contractor_id'] = ContractorResolver::resolve([
            'contractor_id' => $data['vendor_contractor_id'] ?? null,
            'new_contractor_name' => $data['new_vendor_name'] ?? null,
            'new_contractor_type' => 'vendor',
            'new_contractor_phone' => $data['new_vendor_phone'] ?? null,
        ]);
        unset($data['vendor_contractor_id'], $data['new_vendor_name'], $data['new_vendor_phone']);

        $data['is_credit'] = (bool) ($data['is_credit'] ?? false);
        if ($data['is_credit']) {
            $data['payment_account_id'] = null;
            $data['credit_settled_at'] = null;
        }

        $data['work_order_id'] = null;

        return $data;
    }
}
