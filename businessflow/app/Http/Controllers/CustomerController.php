<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Business;
use App\Models\Customer;
use App\Models\PaymentAccount;
use App\Models\Project;
use App\Rules\Phone;
use App\Support\DocumentQr;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
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

    public function store(Request $request): RedirectResponse|JsonResponse
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
            'invoices.payments.account',
            'units.project',
            'units.broker',
            'units.invoices',
            'units.payments.invoice',
            'units.payments.account',
            'units.loan.disbursements.account',
            'units.loan.documents',
            'followups' => fn ($q) => $q->orderByRaw("status = 'done'")->orderBy('due_at'),
            'documents' => fn ($q) => $q->latest(),
        ]);

        $projects = Project::with(['units' => fn ($q) => $q->orderBy('unit_number')])->orderBy('name')->get();
        $paymentAccounts = PaymentAccount::orderBy('name')->get();
        $brokers = Broker::orderBy('name')->get();

        return view('customers.show', compact('customer', 'projects', 'paymentAccounts', 'brokers'));
    }

    public function statement(Customer $customer)
    {
        $customer->load(['units.project', 'units.payments.account', 'invoices.payments.account', 'invoices.project', 'invoices.projectUnit']);
        $business = Business::find(Tenant::id());
        $verifyQr = DocumentQr::dataUri(
            URL::signedRoute('verify.customer', ['customer' => $customer->id])
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

    /**
     * Soft-deletes only — their sale/payment history stays intact in the
     * Ledger and on quotations/invoices for audit purposes (see
     * LedgerController), and they can be restored from the Deleted
     * Customers list if removed by mistake.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('status', 'Customer removed. Their sale history stays visible in the Ledger — restore them anytime from "Deleted customers" below the list.');
    }

    public function trashed(): View
    {
        $customers = Customer::onlyTrashed()->latest('deleted_at')->paginate(20);

        return view('customers.trashed', compact('customers'));
    }

    public function restore(int $id): RedirectResponse
    {
        $customer = Customer::onlyTrashed()->findOrFail($id);
        $customer->restore();

        return redirect()->route('customers.show', $customer)->with('status', 'Customer restored.');
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
            'phone' => ['nullable', 'string', 'max:50', new Phone],
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
