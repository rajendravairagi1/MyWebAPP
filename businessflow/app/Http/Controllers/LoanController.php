<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Loan;
use App\Models\PaymentAccount;
use App\Models\ProjectUnit;
use App\Support\Tenant;
use App\Support\UnitPaymentRecorder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    /**
     * Every bank loan across every customer, one row each — the "which
     * customer owes/received what from which bank" list that used to
     * only exist buried one property at a time on each customer's page.
     */
    public function index(Request $request): View
    {
        $query = Loan::with(['customer', 'unit.project'])
            ->when($request->string('q')->trim()->isNotEmpty(), function ($q) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $q->where(function ($qq) use ($term) {
                    $qq->whereHas('customer', fn ($c) => $c->where('name', 'like', $term))
                        ->orWhereHas('unit', fn ($u) => $u->where('unit_number', 'like', $term))
                        ->orWhereHas('unit.project', fn ($p) => $p->where('name', 'like', $term));
                });
            });

        // Totals reflect every matching loan, not just the current page.
        $allMatching = (clone $query)->get();
        $totals = [
            'count' => $allMatching->count(),
            'sanctioned' => (float) $allMatching->sum('sanctioned_amount'),
            'disbursed' => (float) $allMatching->sum(fn (Loan $l) => $l->totalDisbursed()),
            'remaining' => (float) $allMatching->sum(fn (Loan $l) => $l->remainingToDisburse()),
        ];

        $loans = $query->orderByDesc('created_at')
            ->paginate(\App\Support\ListPagination::perPage($request))
            ->withQueryString();

        $archivedCount = Loan::onlyTrashed()->count();

        return view('loans.index', compact('loans', 'totals', 'archivedCount'));
    }

    public function show(Loan $loan): View
    {
        $loan->load(['customer', 'unit.project', 'disbursements.account', 'disbursements.invoice', 'documents']);
        $accounts = PaymentAccount::orderBy('name')->get();

        return view('loans.show', compact('loan', 'accounts'));
    }

    /**
     * "+ Bank Loan" on a property with none yet — its own page rather
     * than a popup, same reasoning as loans.show replacing the old
     * "Manage" modal: a small popup can still misbehave on a phone (the
     * on-screen keyboard resizing the viewport out from under a
     * height-capped modal is a known source of exactly the "keeps
     * resizing, won't scroll" symptom), where a normal page never has
     * that problem at all.
     */
    public function create(ProjectUnit $unit): View
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before adding a bank loan.');
        abort_if($unit->loan, 422, 'This property already has a bank loan on record.');

        $unit->load('project', 'customer');

        return view('loans.create', compact('unit'));
    }

    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before adding a bank loan.');
        abort_if($unit->loan, 422, 'This property already has a bank loan on record.');

        $loan = $unit->loan()->create($this->validated($request) + ['customer_id' => $unit->customer_id]);

        return redirect()->route('loans.show', $loan)->with('status', 'Bank loan added — add its first disbursement below whenever it comes in.');
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        $loan->update($this->validated($request));

        return back()->with('status', 'Loan details updated.');
    }

    /**
     * "Remove loan" archives it (soft delete) rather than destroying it —
     * same pattern as Admin's Archived Accounts. Every disbursement, its
     * receipt invoice, and the documents on this loan all stay exactly as
     * they were; the loan just drops off the main Loans list until
     * Restore brings it back from archived(). Not back() — the referer
     * for this exact request is the loan's own page, which 404s the
     * instant this archives it.
     */
    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return redirect()->route('loans.index')->with('status', "\"{$loan->bank_name}\" loan archived — restore it any time from Archived Loans.");
    }

    /**
     * Archived (soft-deleted) loans, on their own page — same shape as
     * Admin's Archived Accounts. Disbursements/documents on an archived
     * loan are still exactly what they were; this is only ever reached
     * to Restore one or permanently delete it.
     */
    public function archived(Request $request): View
    {
        $loans = Loan::onlyTrashed()
            ->with(['customer', 'unit.project'])
            ->orderByDesc('deleted_at')
            ->paginate(\App\Support\ListPagination::perPage($request));

        return view('loans.archived', compact('loans'));
    }

    public function restore(int $loan): RedirectResponse
    {
        $loan = Loan::onlyTrashed()->findOrFail($loan);
        $loan->restore();

        return back()->with('status', "\"{$loan->bank_name}\" loan restored.");
    }

    /**
     * Scoped to already-archived loans only, same reasoning as
     * ProjectUnitController::destroyArchived() — this can't be used to
     * skip archiving and delete an active loan by mistake. Disbursements
     * aren't touched (unit_payments.loan_id just goes back to null — see
     * the loans migration); loan_documents cascade-delete with it.
     */
    public function destroyPermanent(int $loan): RedirectResponse
    {
        $loan = Loan::onlyTrashed()->findOrFail($loan);
        $bankName = $loan->bank_name;
        $loan->forceDelete();

        return back()->with('status', "\"{$bankName}\" loan permanently deleted.");
    }

    public function storeDisbursement(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'in:bank_transfer,cheque,neft,rtgs'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'payment_account_id' => ['nullable', 'integer'],
        ]);

        UnitPaymentRecorder::record($loan->unit, [
            'amount' => $data['amount'],
            'purpose' => 'installment',
            'description' => 'Bank loan disbursement — '.$loan->bank_name,
            'method' => $data['method'] ?? 'bank_transfer',
            'paid_at' => $data['paid_at'],
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'loan_id' => $loan->id,
            'payment_account_id' => $data['payment_account_id'] ?? null,
        ]);

        return back()->with('status', 'Disbursement recorded.');
    }

    /**
     * A bank-ready statement for this one loan — sanction details plus
     * every disbursement with its date, method, cheque/reference number
     * and receiving account, so it can be handed to the customer or the
     * bank without having to explain the numbers by hand.
     */
    public function statement(Loan $loan): \Illuminate\Http\Response
    {
        $loan->load(['unit.project', 'customer', 'disbursements.account']);
        $business = Business::find(Tenant::id());

        return Pdf::loadView('loans.statement', compact('loan', 'business'))
            ->download('Loan Statement - '.$loan->bank_name.' - '.$loan->customer->name.'.pdf');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'loan_account_number' => ['nullable', 'string', 'max:100'],
            'sanctioned_amount' => ['required', 'numeric', 'min:0.01'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sanctioned_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
