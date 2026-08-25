<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\ProjectUnit;
use App\Models\UnitPayment;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnitPaymentController extends Controller
{
    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before recording a payment.');

        $payment = $unit->payments()->create($this->validated($request) + [
            'customer_id' => $unit->customer_id,
            'recorded_by' => auth()->id(),
        ]);

        $this->syncReceiptInvoice($unit, $payment);

        $unit->syncPaymentState();

        return back()->with('status', 'Payment recorded.');
    }

    public function update(Request $request, ProjectUnit $unit, UnitPayment $payment): RedirectResponse
    {
        abort_unless($payment->project_unit_id === $unit->id, 404);

        $payment->update($this->validated($request));

        $this->syncReceiptInvoice($unit, $payment);

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

    /**
     * A payment gets its own one-line "receipt" invoice — created the
     * first time, kept in sync (description/amount/date) on every edit
     * after that. Deliberately not routed through Invoice::recordPayment():
     * that method's side effects (flipping the unit straight to "sold" the
     * moment an invoice it created is fully paid) assume one invoice per
     * sale, not one per installment — wrong here, since a single token
     * payment would otherwise mark the whole unit sold. The unit's real
     * status is decided by ProjectUnit::syncPaymentState(), called right
     * after this from store()/update().
     */
    protected function syncReceiptInvoice(ProjectUnit $unit, UnitPayment $payment): Invoice
    {
        $invoice = $payment->invoice ?? new Invoice([
            'customer_id' => $unit->customer_id,
            'project_id' => $unit->project_id,
            'project_unit_id' => $unit->id,
            'unit_payment_id' => $payment->id,
            'counts_toward_property_price' => true,
            'number' => Invoice::nextNumber(Tenant::id()),
            'created_by' => auth()->id(),
        ]);

        $invoice->forceFill([
            'due_date' => $payment->paid_at,
            'status' => 'paid',
            'amount_paid' => $payment->amount,
        ])->save();

        $item = $invoice->items()->first() ?? $invoice->items()->make();
        $item->fill([
            'description' => $payment->description ?: $payment->purposeLabel(),
            'quantity' => 1,
            'unit_price' => $payment->amount,
            'discount' => 0,
            'tax_rate' => 0,
        ])->save();

        $invoice->recalculateTotals();

        return $invoice;
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'purpose' => ['required', 'in:token,installment,registry,maintenance,other'],
            'purpose_other' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'method' => ['nullable', 'string', 'max:50'],
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
