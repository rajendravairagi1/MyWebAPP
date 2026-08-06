<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('products')->latest()->paginate(20);

        return view('customers.index', ['customers' => $customers]);
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $customer = Customer::create($validated);

        $this->storeDocuments($request, $customer);

        return redirect()->route('customers.index')->with('status', "{$customer->name} add ho gaya.");
    }

    public function show(Customer $customer)
    {
        $customer->load(['documents', 'products']);

        return view('customers.show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', ['customer' => $customer]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $this->validated($request, $customer->id);

        $customer->update($validated);

        $this->storeDocuments($request, $customer);

        return redirect()->route('customers.edit', $customer)->with('status', 'Details update ho gayi.');
    }

    public function destroy(Customer $customer)
    {
        foreach ($customer->documents as $document) {
            Storage::disk('public')->delete($document->file_path);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('status', "{$customer->name} delete ho gaya.");
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gst_number' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'opening_balance' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    protected function storeDocuments(Request $request, Customer $customer): void
    {
        if (! $request->hasFile('documents')) {
            return;
        }

        foreach ($request->file('documents') as $file) {
            $path = $file->store('customer-documents', 'public');

            $customer->documents()->create([
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $path,
            ]);
        }
    }
}
