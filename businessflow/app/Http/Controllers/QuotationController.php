<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\Quotation;
use App\Support\DocumentQr;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        $quotations = Quotation::with('customer')->latest()->paginate(20);

        return view('quotations.index', compact('quotations'));
    }

    public function create(Request $request): View
    {
        return view('quotations.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'projects' => Project::with('units')->orderBy('name')->get(),
            'prefillProjectId' => $request->integer('project_id') ?: null,
            'prefillUnitId' => $request->integer('unit_id') ?: null,
            'prefillCustomerId' => $request->integer('customer_id') ?: null,
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

        $projects = $quotation->projectUnit
            ? collect()
            : Project::with(['units' => fn ($q) => $q->orderBy('unit_number')])->orderBy('name')->get();

        return view('quotations.show', compact('quotation', 'projects'));
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
            throw ValidationException::withMessages([
                'project_unit_id' => 'This unit is already assigned to another customer.',
            ]);
        }

        // A unit still shows "available" right up until someone actually
        // pays (see Invoice::recordPayment) — so without this check, two
        // different customers could each have their own quotation/invoice
        // silently pointing at the same not-yet-paid unit, and whoever
        // pays first would win it out from under the other.
        $claimedByAnotherCustomer = Quotation::where('project_unit_id', $unitId)->where('customer_id', '!=', $customerId)->exists()
            || Invoice::where('project_unit_id', $unitId)->where('customer_id', '!=', $customerId)->exists();

        if ($claimedByAnotherCustomer) {
            throw ValidationException::withMessages([
                'project_unit_id' => 'This unit is already linked to another customer\'s quotation or invoice — release it there first, or wait until it\'s available again.',
            ]);
        }
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        if ($quotation->invoices()->exists()) {
            return back()->withErrors(['delete' => "This quotation has already been converted to an invoice and can't be deleted."]);
        }

        $quotation->delete();

        return redirect()->route('quotations.index')->with('status', 'Quotation deleted.');
    }

    public function markSent(Quotation $quotation): RedirectResponse
    {
        $quotation->update(['status' => 'sent']);

        return back()->with('status', 'Marked as sent.');
    }

    public function convert(Request $request, Quotation $quotation): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_unit_id' => ['nullable', 'exists:project_units,id'],
        ]);

        if (! $quotation->project_unit_id && ! empty($data['project_unit_id'])) {
            $this->assertUnitAvailableFor((int) $data['project_unit_id'], $quotation->customer_id);

            $quotation->update([
                'project_id' => $data['project_id'] ?? null,
                'project_unit_id' => $data['project_unit_id'],
            ]);
        }

        $invoice = $quotation->convertToInvoice();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Converted to invoice '.$invoice->number.'.');
    }

    public function pdf(Quotation $quotation)
    {
        $quotation->load(['customer', 'items']);
        $business = Business::find(Tenant::id());
        $verifyQr = DocumentQr::dataUri(
            URL::signedRoute('verify.quotation', ['quotation' => $quotation->id])
        );

        return Pdf::loadView('quotations.pdf', compact('quotation', 'business', 'verifyQr'))
            ->download($quotation->number.'.pdf');
    }
}
