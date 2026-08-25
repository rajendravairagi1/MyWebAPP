<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectUnit;
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

    public function create(Request $request): View
    {
        return view('invoices.create', [
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
            'counts_toward_property_price' => ['nullable', 'boolean'],
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

        $this->assertUnitAvailableFor($data['project_unit_id'] ?? null, $data['customer_id']);

        $invoice = Invoice::create([
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'] ?? null,
            'project_unit_id' => $data['project_unit_id'] ?? null,
            'counts_toward_property_price' => $request->boolean('counts_toward_property_price', true),
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
        $invoice->load(['customer', 'items.product', 'payments', 'project', 'projectUnit']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        abort_if($invoice->payments()->exists(), 403, 'This invoice already has payments recorded and can no longer be edited.');

        $invoice->load('items');

        return view('invoices.edit', [
            'invoice' => $invoice,
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'projects' => Project::with('units')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_if($invoice->payments()->exists(), 403, 'This invoice already has payments recorded and can no longer be edited.');

        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'project_unit_id' => ['nullable', 'exists:project_units,id'],
            'counts_toward_property_price' => ['nullable', 'boolean'],
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

        $this->assertUnitAvailableFor($data['project_unit_id'] ?? null, $data['customer_id']);

        $this->syncProjectUnit($invoice->project_unit_id, $data['project_unit_id'] ?? null, $invoice->customer_id);

        $invoice->update([
            'customer_id' => $data['customer_id'],
            'project_id' => $data['project_id'] ?? null,
            'project_unit_id' => $data['project_unit_id'] ?? null,
            'counts_toward_property_price' => $request->boolean('counts_toward_property_price', true),
            'due_date' => $data['due_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $invoice->items()->delete();

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

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice updated.');
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

    protected function syncProjectUnit(?int $oldUnitId, ?int $newUnitId, int $oldCustomerId): void
    {
        // Booking (status -> 'booked') only ever happens when the first
        // payment is recorded (see Invoice::recordPayment), so there is
        // nothing to book here when the unit changes — only a
        // previously-booked unit needs releasing if it's no longer
        // attached to this invoice.
        if ($oldUnitId === $newUnitId || ! $oldUnitId) {
            return;
        }

        ProjectUnit::where('id', $oldUnitId)
            ->where('customer_id', $oldCustomerId)
            ->where('status', 'booked')
            ->update(['status' => 'available', 'customer_id' => null]);
    }

    public function markSent(Invoice $invoice): RedirectResponse
    {
        $invoice->update(['status' => $invoice->amount_paid > 0 ? $invoice->status : 'sent']);

        return back()->with('status', 'Marked as sent.');
    }

    public function pdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items', 'projectUnit']);
        $business = \App\Models\Business::find(Tenant::id());
        $verifyQr = \App\Support\DocumentQr::dataUri(
            \Illuminate\Support\Facades\URL::signedRoute('verify.invoice', ['invoice' => $invoice->id])
        );

        return Pdf::loadView('invoices.pdf', compact('invoice', 'business', 'verifyQr'))
            ->download($invoice->number.'.pdf');
    }
}
