<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\Quotation;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::with('customer')->latest()->paginate(20);

        return view('quotations.index', compact('quotations'));
    }

    public function create(): View
    {
        return view('quotations.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'projects' => Project::with(['units' => fn ($q) => $q->where('status', 'available')])->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_unit_id' => ['nullable', 'exists:project_units,id'],
            'valid_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->assertUnitAvailableFor($data['project_unit_id'] ?? null, $data['customer_id']);

        $quotation = Quotation::create([
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'] ?? null,
            'project_unit_id' => $data['project_unit_id'] ?? null,
            'number' => Quotation::nextNumber(Tenant::id()),
            'status' => 'draft',
            'valid_until' => $data['valid_until'] ?? null,
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
            'created_by' => auth()->id(),
        ]);

        foreach ($data['items'] as $item) {
            $quotation->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 0,
            ]);
        }

        $quotation->recalculateTotals();

        return redirect()->route('quotations.show', $quotation)->with('status', 'Quotation created.');
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['customer', 'items.product', 'invoices', 'project', 'projectUnit']);

        return view('quotations.show', compact('quotation'));
    }

    public function edit(Quotation $quotation): View
    {
        abort_if($quotation->invoices()->exists(), 403, 'This quotation has already been converted to an invoice and can no longer be edited.');

        $quotation->load('items');

        return view('quotations.edit', [
            'quotation' => $quotation,
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'projects' => Project::with('units')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        abort_if($quotation->invoices()->exists(), 403, 'This quotation has already been converted to an invoice and can no longer be edited.');

        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_unit_id' => ['nullable', 'exists:project_units,id'],
            'valid_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'terms' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->assertUnitAvailableFor($data['project_unit_id'] ?? null, $data['customer_id']);

        $quotation->update([
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'] ?? null,
            'project_unit_id' => $data['project_unit_id'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'notes' => $data['notes'] ?? null,
            'terms' => $data['terms'] ?? null,
        ]);

        $quotation->items()->delete();

        foreach ($data['items'] as $item) {
            $quotation->items()->create([
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'discount' => $item['discount'] ?? 0,
                'tax_rate' => $item['tax_rate'] ?? 0,
            ]);
        }

        $quotation->recalculateTotals();

        return redirect()->route('quotations.show', $quotation)->with('status', 'Quotation updated.');
    }

    protected function assertUnitAvailableFor(?int $unitId, int $customerId): void
    {
        if (! $unitId) {
            return;
        }

        $unit = ProjectUnit::find($unitId);

        if ($unit && $unit->status !== 'available' && (int) $unit->customer_id !== $customerId) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'project_unit_id' => 'This unit is already assigned to another customer.',
            ]);
        }
    }

    public function markSent(Quotation $quotation): RedirectResponse
    {
        $quotation->update(['status' => 'sent']);

        return back()->with('status', 'Marked as sent.');
    }

    public function convert(Quotation $quotation): RedirectResponse
    {
        $invoice = $quotation->convertToInvoice();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Converted to invoice '.$invoice->number.'.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        $business = \App\Models\Business::find(Tenant::id());
        $verifyQr = \App\Support\DocumentQr::dataUri(
            \Illuminate\Support\Facades\URL::signedRoute('verify.quotation', ['quotation' => $quotation->id])
        );

        return Pdf::loadView('quotations.pdf', compact('quotation', 'business', 'verifyQr'))
            ->download($quotation->number.'.pdf');
    }
}
