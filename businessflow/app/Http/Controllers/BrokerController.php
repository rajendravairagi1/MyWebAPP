<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\BrokerTransaction;
use App\Models\Business;
use App\Models\ProjectUnit;
use App\Models\PropertyDeal;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrokerController extends Controller
{
    public function index(): View
    {
        $brokers = Broker::withCount('transactions')->orderBy('name')->get();

        return view('brokers.index', compact('brokers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $broker = Broker::create($this->validated($request));

        return redirect()->route('brokers.show', $broker)->with('status', 'Broker added.');
    }

    public function show(Broker $broker): View
    {
        $broker->load('transactions.unit.project', 'transactions.deal', 'documents');
        $units = ProjectUnit::with('project')
            ->where('status', '!=', 'available')
            ->whereNull('archived_at')
            ->get()
            ->sortBy([['project.name', 'asc'], ['unit_number', 'asc']]);

        // Only sold deals have a sale price to base a % commission on —
        // an open deal's eventual sale price isn't known yet.
        $deals = PropertyDeal::whereNotNull('sale_price')->orderByDesc('deal_date')->get();

        return view('brokers.show', compact('broker', 'units', 'deals'));
    }

    /**
     * Full ledger for this broker — every commission earned and every
     * payment paid out, with the running balance — so it can be handed
     * over as proof of account instead of reading numbers off screen.
     */
    public function statement(Broker $broker): \Illuminate\Http\Response
    {
        $broker->load('transactions.unit.project', 'transactions.deal');
        $business = Business::find(Tenant::id());

        return Pdf::loadView('brokers.statement', compact('broker', 'business'))
            ->download('Broker Statement - '.$broker->name.'.pdf');
    }

    /**
     * A billing document for the balance currently due — every commission
     * line earned, less what's already been paid, netting to the amount
     * being asked for now. Meant to actually hand or send to the broker,
     * unlike the full Statement which is a record of everything that
     * ever happened (earned and paid) rather than a bill.
     */
    public function invoice(Broker $broker): \Illuminate\Http\Response
    {
        abort_if($broker->balance() <= 0, 422, 'Nothing is currently owed to this broker.');

        $broker->load('transactions.unit.project', 'transactions.deal');
        $business = Business::find(Tenant::id());
        $commissionLines = $broker->transactions->where('type', 'commission_accrued');

        return Pdf::loadView('brokers.invoice', compact('broker', 'business', 'commissionLines'))
            ->download('Commission Invoice - '.$broker->name.'.pdf');
    }

    public function update(Request $request, Broker $broker): RedirectResponse
    {
        $broker->update($this->validated($request));

        return back()->with('status', 'Broker updated.');
    }

    public function destroy(Broker $broker): RedirectResponse
    {
        $broker->delete();

        return redirect()->route('brokers.index')->with('status', 'Broker deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    public function storeTransaction(Request $request, Broker $broker): RedirectResponse
    {
        $data = $this->validatedTransaction($request);

        $broker->transactions()->create($data + ['recorded_by' => auth()->id()]);

        return back()->with('status', (new BrokerTransaction($data))->typeLabel().' recorded.');
    }

    public function updateTransaction(Request $request, Broker $broker, BrokerTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->broker_id === $broker->id, 404);

        $transaction->update($this->validatedTransaction($request));

        return back()->with('status', 'Transaction updated.');
    }

    public function destroyTransaction(Broker $broker, BrokerTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->broker_id === $broker->id, 404);

        $transaction->delete();

        return back()->with('status', 'Transaction removed.');
    }

    /**
     * A commission can be typed in directly (fixed amount) or calculated
     * as a % of the linked property's price — either way the resolved
     * figure lands in `amount`, which stays the single source of truth
     * for every total this model computes. The % base is the unit's
     * price for a normal sale, or the resale deal's sale price for a
     * Property Deal — never both at once.
     */
    protected function validatedTransaction(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:commission_accrued,payment_paid'],
            'commission_mode' => ['nullable', 'in:percent,fixed'],
            'project_unit_id' => ['nullable', 'integer'],
            'property_deal_id' => ['nullable', 'integer'],
            'commission_percent' => ['nullable', 'numeric', 'min:0.01', 'max:100'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $unit = null;
        if (! empty($data['project_unit_id'])) {
            $unit = ProjectUnit::findOrFail($data['project_unit_id']);
        } else {
            $data['project_unit_id'] = null;
        }

        $deal = null;
        if (! empty($data['property_deal_id'])) {
            $deal = PropertyDeal::findOrFail($data['property_deal_id']);
        } else {
            $data['property_deal_id'] = null;
        }

        if ($data['type'] === 'commission_accrued' && ($data['commission_mode'] ?? null) === 'percent') {
            $base = $unit?->price ?? $deal?->sale_price;
            abort_unless($base, 422, 'Select a property or a sold deal to calculate a percentage commission.');
            abort_unless($data['commission_percent'] ?? null, 422, 'Enter a commission percentage.');
            $data['amount'] = round((float) $base * (float) $data['commission_percent'] / 100, 2);
        } else {
            abort_unless($data['amount'] ?? null, 422, 'Enter a commission amount.');
            $data['commission_percent'] = null;
        }

        unset($data['commission_mode']);

        return $data;
    }
}
