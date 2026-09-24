<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use App\Models\ProjectUnit;
use App\Models\UnitPayment;
use App\Support\UnitPaymentRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitPaymentController extends Controller
{
    /**
     * "Record Payment" on a property — its own page, same reasoning as
     * loans.create replacing the "+ Bank Loan" modal: this form has
     * enough in it (payment source, purpose, amount/date/method/
     * reference/account) that a height-capped popup could clip on a
     * short screen or resize under a phone's on-screen keyboard.
     */
    public function create(ProjectUnit $unit): View
    {
        $unit->load('project', 'customer', 'loan');
        $paymentAccounts = PaymentAccount::orderBy('name')->get();

        return view('unit-payments.create', compact('unit', 'paymentAccounts'));
    }

    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        UnitPaymentRecorder::record($unit, $this->validated($request));

        return redirect()->route('customers.show', $unit->customer_id)->with('status', 'Payment recorded.');
    }

    public function update(Request $request, ProjectUnit $unit, UnitPayment $payment): RedirectResponse
    {
        abort_unless($payment->project_unit_id === $unit->id, 404);

        $payment->update($this->validated($request));

        UnitPaymentRecorder::syncReceiptInvoice($unit, $payment);

        $unit->syncPaymentState();

        return back()->with('status', 'Payment updated.');
    }

    public function destroy(ProjectUnit $unit, UnitPayment $payment): RedirectResponse
    {
        abort_unless($payment->project_unit_id === $unit->id, 404);

        // The receipt invoice created for this payment (if any) cascades
        // away with it at the database level — see the unit_payment_id
        // foreign key on invoices.
        $payment->delete();

        $unit->syncPaymentState();

        return back()->with('status', 'Payment removed.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'purpose' => ['required', 'in:token,installment,registry,maintenance,other'],
            'purpose_other' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'method' => ['nullable', 'string', 'max:50'],
            'payment_account_id' => ['nullable', 'integer'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['purpose'] === 'other' && filled($data['purpose_other'] ?? null)) {
            $data['purpose'] = $data['purpose_other'];
        }
        unset($data['purpose_other']);

        return $data;
    }
}
