<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\RawMaterial;
use App\Models\Supplier;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $purchases = Purchase::with(['supplier', 'rawMaterial'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('purchase_date')
            ->paginate(20)
            ->withQueryString();

        return view('purchases.index', ['purchases' => $purchases, 'status' => $status]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $materials = RawMaterial::orderBy('name')->get();

        return view('purchases.create', ['suppliers' => $suppliers, 'materials' => $materials]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'bill_number' => ['nullable', 'string', 'max:100'],
            'purchase_date' => ['required', 'date'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'rate' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $amount = round($validated['quantity'] * $validated['rate'], 2);

        $purchase = Purchase::create([
            ...$validated,
            'amount' => $amount,
        ]);

        RawMaterial::where('id', $validated['raw_material_id'])->increment('current_stock', $validated['quantity']);

        return redirect()->route('purchases.show', $purchase)->with('status', 'Purchase entry ho gayi aur stock update ho gaya.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'rawMaterial', 'payments']);

        return view('purchases.show', ['purchase' => $purchase]);
    }

    public function addPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$purchase->balanceDue()],
            'paid_at' => ['required', 'date'],
            'method' => ['required', 'in:cash,bank_transfer,cheque,upi,other'],
            'remarks' => ['nullable', 'string'],
        ]);

        $purchase->payments()->create($validated);

        $paid = $purchase->totalPaid();
        $purchase->update([
            'status' => $paid >= $purchase->amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
        ]);

        return back()->with('status', 'Payment record ho gaya.');
    }
}
