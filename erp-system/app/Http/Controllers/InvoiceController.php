<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $invoices = Invoice::with('customer')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('invoice_date')
            ->paginate(20)
            ->withQueryString();

        return view('invoices.index', ['invoices' => $invoices, 'status' => $status]);
    }

    public function create(Job $job)
    {
        if ($job->status !== 'ready') {
            return back()->with('status', 'Sirf "Ready" jobs ke liye invoice bana sakte ho.');
        }

        if ($job->invoice) {
            return redirect()->route('invoices.show', $job->invoice);
        }

        $job->load(['items.product', 'customer']);

        return view('invoices.create', ['job' => $job]);
    }

    public function store(Request $request, Job $job)
    {
        if ($job->invoice) {
            return redirect()->route('invoices.show', $job->invoice);
        }

        $validated = $request->validate([
            'challan_number' => ['nullable', 'string', 'max:100'],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'vehicle_number' => ['nullable', 'string', 'max:50'],
            'driver_name' => ['nullable', 'string', 'max:100'],
            'driver_mobile' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
        ]);

        $job->load('items.product');

        $subtotal = $job->items->sum(fn ($item) => $item->quantity * $item->product->rate);
        $cgst = round($subtotal * 0.09, 2);
        $sgst = round($subtotal * 0.09, 2);
        $total = $subtotal + $cgst + $sgst;

        $invoice = Invoice::create([
            ...$validated,
            'job_order_id' => $job->id,
            'customer_id' => $job->customer_id,
            'invoice_number' => $this->nextInvoiceNumber(),
            'subtotal' => $subtotal,
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'total' => $total,
        ]);

        foreach ($job->items as $item) {
            $invoice->items()->create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'rate' => $item->product->rate,
                'amount' => $item->quantity * $item->product->rate,
            ]);
        }

        Customer::where('id', $job->customer_id)->increment('opening_balance', $total);

        return redirect()->route('invoices.show', $invoice)->with('status', "Invoice {$invoice->invoice_number} ban gayi.");
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'job', 'items.product', 'payments']);

        return view('invoices.show', ['invoice' => $invoice]);
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$invoice->balanceDue()],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank_transfer,cheque,upi,other'],
            'remarks' => ['nullable', 'string'],
        ]);

        $invoice->payments()->create($validated);

        Customer::where('id', $invoice->customer_id)->decrement('opening_balance', $validated['amount']);

        $paid = $invoice->totalPaid();
        $invoice->update([
            'status' => $paid >= $invoice->total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        return back()->with('status', 'Payment record ho gaya.');
    }

    protected function nextInvoiceNumber(): string
    {
        $year = date('Y');
        $count = Invoice::whereYear('invoice_date', $year)->count() + 1;

        return "INV/{$year}/".str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}
