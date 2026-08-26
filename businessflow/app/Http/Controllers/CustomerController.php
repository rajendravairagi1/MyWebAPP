<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Project;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

    public function store(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $data = $this->validated($request);
        $data = $this->withUploads($request, $data);

        $customer = Customer::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $customer->id, 'name' => $customer->name]);
        }

        return redirect()->route('customers.show', $customer)->with('status', 'Customer added.');
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'quotations' => fn ($q) => $q->latest()->limit(10),
            'invoices' => fn ($q) => $q->latest()->limit(10),
            'units.project',
            'units.invoices',
            'units.payments.invoice',
            'followups' => fn ($q) => $q->orderByRaw("status = 'done'")->orderBy('due_at'),
            'documents' => fn ($q) => $q->latest(),
        ]);

        $projects = Project::with(['units' => fn ($q) => $q->orderBy('unit_number')])->orderBy('name')->get();

        return view('customers.show', compact('customer', 'projects'));
    }

    public function statement(Customer $customer)
    {
        $customer->load(['units.project', 'units.payments', 'invoices.payments', 'invoices.project', 'invoices.projectUnit']);
        $business = Business::find(Tenant::id());
        $verifyQr = \App\Support\DocumentQr::dataUri(
            \Illuminate\Support\Facades\URL::signedRoute('verify.customer', ['customer' => $customer->id])
        );

        return Pdf::loadView('customers.statement', compact('customer', 'business', 'verifyQr'))
            ->download('Statement - '.$customer->name.'.pdf');
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validated($request);
        $data = $this->withUploads($request, $data, $customer);

        $customer->update($data);

        return redirect()->route('customers.show', $customer)->with('status', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        // Quotations/invoices restrict-on-delete at the DB level (they're
        // financial records that shouldn't silently vanish) — check first
        // so this fails with a clear message instead of a raw SQL error.
        $blockers = [];
        if ($customer->quotations()->exists()) {
            $blockers[] = 'quotations';
        }
        if ($customer->invoices()->exists()) {
            $blockers[] = 'invoices';
        }

        if ($blockers) {
            return back()->withErrors([
                'delete' => 'Can\'t delete this customer — they still have '.implode(' and ', $blockers).'. Remove those first.',
            ]);
        }

        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer removed.');
    }

    public function photo(Customer $customer): StreamedResponse
    {
        abort_unless(filled($customer->photo_path), 404);

        return Storage::disk('local')->response($customer->photo_path);
    }

    public function aadhar(Customer $customer): StreamedResponse
    {
        abort_unless(filled($customer->aadhar_path), 404);

        return Storage::disk('local')->response($customer->aadhar_path, $customer->aadhar_name);
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
            'photo' => ['nullable', 'image', 'max:5120'],
            'aadhar' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
    }

    protected function withUploads(Request $request, array $data, ?Customer $customer = null): array
    {
        if ($request->hasFile('photo')) {
            if ($customer?->photo_path) {
                Storage::disk('local')->delete($customer->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('customer-photos', 'local');
        }
        unset($data['photo']);

        if ($request->hasFile('aadhar')) {
            if ($customer?->aadhar_path) {
                Storage::disk('local')->delete($customer->aadhar_path);
            }
            $file = $request->file('aadhar');
            $data['aadhar_path'] = $file->store('customer-aadhar', 'local');
            $data['aadhar_name'] = $file->getClientOriginalName();
        }
        unset($data['aadhar']);

        return $data;
    }
}
