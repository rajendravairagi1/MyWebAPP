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
    public function index(): View
    {
        $loans = Loan::with(['customer', 'unit.project'])
            ->orderByDesc('created_at')
            ->get();

        $totals = [
            'count' => $loans->count(),
            'sanctioned' => (float) $loans->sum('sanctioned_amount'),
            'disbursed' => (float) $loans->sum(fn (Loan $l) => $l->totalDisbursed()),
            'remaining' => (float) $loans->sum(fn (Loan $l) => $l->remainingToDisburse()),
        ];

        return view('loans.index', compact('loans', 'totals'));
    }

    public function show(Loan $loan): View
    {
        $loan->load(['customer', 'unit.project', 'disbursements.account', 'documents']);
        $accounts = PaymentAccount::orderBy('name')->get();

        return view('loans.show', compact('loan', 'accounts'));
    }

    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before adding a bank loan.');
        abort_if($unit->loan, 422, 'This property already has a bank loan on record.');

        $unit->loan()->create($this->validated($request) + ['customer_id' => $unit->customer_id]);

        return back()->with('status', 'Bank loan added.');
    }

    public function update(Request $request, Loan $loan): RedirectResponse
    {
        $loan->update($this->validated($request));

        return back()->with('status', 'Loan details updated.');
    }

    /**
     * Removing the loan record itself never removes the money already
     * disbursed — those stay as ordinary payments in the unit's ledger,
     * they just stop being grouped under a "loan" going forward.
     */
    public function destroy(Loan $loan): RedirectResponse
    {
        $loan->delete();

        return back()->with('status', 'Loan record removed — disbursements already recorded stay in the payment ledger.');
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
