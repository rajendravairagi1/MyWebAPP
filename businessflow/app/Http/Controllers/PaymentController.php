<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.max(0.01, $invoice->balanceDue())],
            'method' => ['nullable', 'string', 'max:50'],
            'payment_account_id' => ['nullable', 'integer'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $invoice->recordPayment(
            amount: (float) $data['amount'],
            method: $data['method'] ?? null,
            paidAt: $data['paid_at'],
            reference: $data['reference'] ?? null,
            notes: $data['notes'] ?? null,
            paymentAccountId: $data['payment_account_id'] ?? null,
        );

        return back()->with('status', 'Payment recorded.');
    }

    public function update(Request $request, Invoice $invoice, Payment $payment): RedirectResponse
    {
        abort_unless($payment->invoice_id === $invoice->id, 404);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['nullable', 'string', 'max:50'],
            'payment_account_id' => ['nullable', 'integer'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $payment->update($data);

        $totalPaid = $invoice->payments()->sum('amount');
        $status = match (true) {
            $totalPaid <= 0 => 'sent',
            $totalPaid >= $invoice->total => 'paid',
            default => 'partially_paid',
        };

        $invoice->forceFill(['amount_paid' => $totalPaid, 'status' => $status])->save();

        return back()->with('status', 'Payment updated.');
    }

    public function destroy(Invoice $invoice, Payment $payment): RedirectResponse
    {
        abort_unless($payment->invoice_id === $invoice->id, 404);

        $payment->delete();

        $totalPaid = $invoice->payments()->sum('amount');
        $status = match (true) {
            $totalPaid <= 0 => 'sent',
            $totalPaid >= $invoice->total => 'paid',
            default => 'partially_paid',
        };

        $invoice->forceFill(['amount_paid' => $totalPaid, 'status' => $status])->save();

        return back()->with('status', 'Payment removed.');
    }
}
