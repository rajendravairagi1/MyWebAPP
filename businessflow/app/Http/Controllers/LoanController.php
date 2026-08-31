<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\ProjectUnit;
use App\Support\UnitPaymentRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
{
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
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        UnitPaymentRecorder::record($loan->unit, [
            'amount' => $data['amount'],
            'purpose' => 'installment',
            'description' => 'Bank loan disbursement — '.$loan->bank_name,
            'method' => 'bank_transfer',
            'paid_at' => $data['paid_at'],
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
            'loan_id' => $loan->id,
        ]);

        return back()->with('status', 'Disbursement recorded.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'loan_account_number' => ['nullable', 'string', 'max:100'],
            'sanctioned_amount' => ['required', 'numeric', 'min:0.01'],
            'sanctioned_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
