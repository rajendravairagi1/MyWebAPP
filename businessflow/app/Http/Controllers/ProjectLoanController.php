<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectLoan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectLoanController extends Controller
{
    /**
     * Every construction loan the builder has taken, across every
     * project — mirrors loans.index but for the builder's own financing
     * rather than a customer's home loan.
     */
    public function index(Request $request): View
    {
        $query = ProjectLoan::with('project')
            ->when($request->string('q')->trim()->isNotEmpty(), function ($q) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('lender_name', 'like', $term)
                        ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $term));
                });
            });

        $allMatching = (clone $query)->get();
        $totals = [
            'count' => $allMatching->count(),
            'principal' => (float) $allMatching->sum('principal_amount'),
            'outstanding' => (float) $allMatching->sum(fn (ProjectLoan $l) => $l->totalOutstanding()),
            'interest_paid' => (float) $allMatching->sum(fn (ProjectLoan $l) => $l->totalInterestPaid()),
        ];

        $loans = $query->orderByDesc('created_at')
            ->paginate(\App\Support\ListPagination::perPage($request))
            ->withQueryString();

        $projects = Project::orderBy('name')->get();

        return view('project-loans.index', compact('loans', 'totals', 'projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $loan = ProjectLoan::create($this->validated($request));

        return redirect()->route('project-loans.show', $loan)->with('status', 'Project loan added.');
    }

    public function show(ProjectLoan $projectLoan): View
    {
        $projectLoan->load(['project', 'payments.recorder']);

        return view('project-loans.show', ['loan' => $projectLoan]);
    }

    public function update(Request $request, ProjectLoan $projectLoan): RedirectResponse
    {
        $projectLoan->update($this->validated($request));
        $projectLoan->recalculateSplits();

        return back()->with('status', 'Loan details updated.');
    }

    public function destroy(ProjectLoan $projectLoan): RedirectResponse
    {
        $projectLoan->delete();

        return redirect()->route('project-loans.index')->with('status', 'Project loan removed.');
    }

    public function storePayment(Request $request, ProjectLoan $projectLoan): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $projectLoan->payments()->create($data + ['recorded_by' => $request->user()->id]);
        $projectLoan->recalculateSplits();

        return back()->with('status', 'Payment recorded.');
    }

    public function destroyPayment(ProjectLoan $projectLoan, \App\Models\ProjectLoanPayment $payment): RedirectResponse
    {
        abort_if($payment->project_loan_id !== $projectLoan->id, 404);

        $payment->delete();
        $projectLoan->recalculateSplits();

        return back()->with('status', 'Payment removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'project_id' => ['required', Rule::exists('projects', 'id')->where(fn ($q) => $q->where('business_id', \App\Support\Tenant::id()))],
            'lender_name' => ['required', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'principal_amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'repayment_type' => ['required', 'in:emi,full_payment'],
            'tenure_months' => ['nullable', 'integer', 'min:1', 'max:600'],
            'disbursed_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
