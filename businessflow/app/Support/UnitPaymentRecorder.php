<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\ProjectUnit;
use App\Models\UnitPayment;

/**
 * Records a payment against a property unit — used by the normal
 * "Record Payment" flow (UnitPaymentController) and by bank loan
 * disbursements (LoanController), which are just payments tagged with
 * a loan_id so they still count toward the unit's Collected total like
 * any other payment. Kept as one place so both paths stay in sync: a
 * receipt invoice and the unit's sold/available status.
 */
class UnitPaymentRecorder
{
    public static function record(ProjectUnit $unit, array $data): UnitPayment
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before recording a payment.');

        $payment = $unit->payments()->create($data + [
            'customer_id' => $unit->customer_id,
            'recorded_by' => auth()->id(),
        ]);

        static::syncReceiptInvoice($unit, $payment);

        $unit->syncPaymentState();

        return $payment;
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
     * after this.
     */
    public static function syncReceiptInvoice(ProjectUnit $unit, UnitPayment $payment): Invoice
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
}
