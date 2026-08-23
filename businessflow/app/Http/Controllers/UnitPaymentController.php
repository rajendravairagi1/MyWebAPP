<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use App\Models\UnitPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UnitPaymentController extends Controller
{
    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        abort_if(! $unit->customer_id, 422, 'Assign this property to a customer before recording a payment.');

        $unit->payments()->create($this->validated($request) + [
            'customer_id' => $unit->customer_id,
            'recorded_by' => auth()->id(),
        ]);

        $unit->syncPaymentState();

        return back()->with('status', 'Payment recorded.');
    }

    public function update(Request $request, ProjectUnit $unit, UnitPayment $payment): RedirectResponse
    {
        abort_unless($payment->project_unit_id === $unit->id, 404);

        $payment->update($this->validated($request));

        $unit->syncPaymentState();

        return back()->with('status', 'Payment updated.');
    }

    public function destroy(ProjectUnit $unit, UnitPayment $payment): RedirectResponse
    {
        abort_unless($payment->project_unit_id === $unit->id, 404);

        $payment->delete();

        $unit->syncPaymentState();

        return back()->with('status', 'Payment removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['nullable', 'string', 'max:50'],
            'paid_at' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
