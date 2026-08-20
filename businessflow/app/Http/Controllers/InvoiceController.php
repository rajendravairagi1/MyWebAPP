<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $invoices = Invoice::with('customer')
            ->when($request->string('status')->isNotEmpty(), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        return view('invoices.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $invoice = Invoice::create([
            'customer_id' => $data['customer_id'],
            'number' => Invoice::nextNumber(Tenant::id()),
            'status' => 'draft',
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            $invoice->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 0,
            ]);
        }

        $invoice->recalculateTotals();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice created.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items.product', 'payments']);

        return view('invoices.show', compact('invoice'));
    }

    public function markSent(Invoice $invoice): RedirectResponse
    {
        $invoice->update(['status' => $invoice->amount_paid > 0 ? $invoice->status : 'sent']);

        return back()->with('status', 'Marked as sent.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        $business = \App\Models\Business::find(Tenant::id());

        return Pdf::loadView('invoices.pdf', compact('invoice', 'business'))
            ->download($invoice->number.'.pdf');
    }
}
