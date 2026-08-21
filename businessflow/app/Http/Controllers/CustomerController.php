<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Project;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->when($request->string('q')->trim()->isNotEmpty(), fn ($query) => $query->where(function ($q) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $q->where('name', 'like', $term)
                    ->orWhere('company', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhere('phone', 'like', $term);
            }))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)->with('status', 'Customer added.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'quotations' => fn ($q) => $q->latest()->limit(10),
            'invoices' => fn ($q) => $q->latest()->limit(10),
            'units.project',
            'units.invoices',
            'followups' => fn ($q) => $q->orderByRaw("status = 'done'")->orderBy('due_at'),
            'documents' => fn ($q) => $q->latest(),
        ]);

        $projects = Project::with(['units' => fn ($q) => $q->orderBy('unit_number')])->orderBy('name')->get();

        return view('customers.show', compact('customer', 'projects'));
    }

    public function statement(Customer $customer)
    {
        $customer->load(['units.project', 'invoices.payments', 'invoices.project', 'invoices.projectUnit']);
        $business = Business::find(Tenant::id());

        return Pdf::loadView('customers.statement', compact('customer', 'business'))
            ->download('Statement - '.$customer->name.'.pdf');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request));

        return redirect()->route('customers.show', $customer)->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:100'],
        ]);
    }
}
