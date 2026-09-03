<?php

namespace App\Http\Controllers;

use App\Models\Contractor;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Support\ContractorResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectCostController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate($this->rules());

        $data = $this->applyCategory($data);
        $data = $this->applyCreditRules($request, $data);
        $data = $this->applyContractor($data);

        if ($request->hasFile('bill')) {
            $file = $request->file('bill');
            $data['bill_path'] = $file->store('project-bills/'.$project->id, 'local');
            $data['bill_name'] = $file->getClientOriginalName();
        }
        unset($data['bill']);

        $project->costs()->create($data);

        return back()->with('status', $data['is_credit'] ? 'Payment added — marked as Udhar (unpaid), showing on the Material Udhar page.' : 'Payment added.');
    }

    public function update(Request $request, Project $project, ProjectCost $cost): RedirectResponse
    {
        abort_unless($cost->project_id === $project->id, 404);

        $data = $request->validate($this->rules());

        $data = $this->applyCategory($data);
        $data = $this->applyCreditRules($request, $data);
        $data = $this->applyContractor($data);

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

    protected function rules(): array
    {
        return [
            'category' => ['required', 'in:land,construction,material,labor,approval,marketing,other'],
            'category_other' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'spent_on' => ['required', 'date'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'contractor_id' => ['nullable', 'integer'],
            'new_contractor_name' => ['nullable', 'string', 'max:255'],
            'new_contractor_type' => ['nullable', 'string', 'in:'.implode(',', array_keys(Contractor::TYPES))],
            'new_contractor_type_other' => ['nullable', 'string', 'max:100'],
            'new_contractor_phone' => ['nullable', 'string', 'max:30'],
            'payment_account_id' => ['nullable', 'integer'],
            'is_credit' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'bill' => ['nullable', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,webp'],
        ];
    }

    /**
     * Resolves the "paid to" contractor from either the dropdown or the
     * inline "+ new" fields, then drops the inline-only inputs so they
     * never reach ProjectCost::create()/update() as stray attributes.
     */
    protected function applyContractor(array $data): array
    {
        $data['contractor_id'] = ContractorResolver::resolve($data);
        unset($data['new_contractor_name'], $data['new_contractor_type'], $data['new_contractor_type_other'], $data['new_contractor_phone']);

        return $data;
    }

    protected function applyCategory(array $data): array
    {
        if ($data['category'] === 'other' && filled($data['category_other'] ?? null)) {
            $data['category'] = $data['category_other'];
        }
        unset($data['category_other']);

        return $data;
    }

    /**
     * Udhar (credit) means nothing has been paid yet — no account, no
     * settle date, regardless of what the hidden dropdown happened to
     * submit. Un-checking it just means this was a normal payment.
     */
    protected function applyCreditRules(Request $request, array $data): array
    {
        $data['is_credit'] = $request->boolean('is_credit');

        if ($data['is_credit']) {
            $data['payment_account_id'] = null;
            $data['credit_settled_at'] = null;
        }

        return $data;
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
