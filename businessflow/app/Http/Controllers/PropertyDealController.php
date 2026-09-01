<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\PropertyDeal;
use App\Support\BrokerResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyDealController extends Controller
{
    public function index(): View
    {
        $deals = PropertyDeal::with('broker')->orderByDesc('deal_date')->orderByDesc('id')->get();

        $sold = $deals->where('status', 'sold');

        $totals = [
            'count' => $deals->count(),
            'open' => $deals->where('status', 'open')->count(),
            'total_purchase' => (float) $sold->sum('purchase_price'),
            'total_sale' => (float) $sold->sum('sale_price'),
            'total_profit' => (float) $sold->sum(fn (PropertyDeal $d) => $d->profit()),
        ];

        $brokers = Broker::orderBy('name')->get();

        return view('property-deals.index', compact('deals', 'totals', 'brokers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['broker_id'] = BrokerResolver::resolve($data);
        $data = $this->resolveStatus($data, null);

        PropertyDeal::create($data);

        return back()->with('status', 'Deal added.');
    }

    public function update(Request $request, PropertyDeal $deal): RedirectResponse
    {
        $data = $this->validated($request);
        $data['broker_id'] = BrokerResolver::resolve($data);
        $data = $this->resolveStatus($data, $deal);

        $deal->update($data);

        return back()->with('status', 'Deal updated.');
    }

    /**
     * A deal counts as "sold" the moment it has a sale price — whether
     * that was typed in on the very first save or added later via edit —
     * so the summary totals and status badge never disagree with the
     * profit already showing on the row. Cancelling is still explicit.
     */
    protected function resolveStatus(array $data, ?PropertyDeal $deal): array
    {
        if ($data['status'] === 'cancelled') {
            return $data;
        }

        if (! empty($data['sale_price'])) {
            $data['status'] = 'sold';

            // Selling for the first time (or re-marking sold) without an
            // explicit date defaults to today, same as elsewhere in the app.
            if (empty($data['sold_date']) && ! $deal?->sold_date) {
                $data['sold_date'] = now()->toDateString();
            }
        } else {
            $data['status'] = 'open';
            $data['sold_date'] = null;
        }

        return $data;
    }

    public function destroy(PropertyDeal $deal): RedirectResponse
    {
        $deal->delete();

        return back()->with('status', 'Deal removed.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'property_title' => ['required', 'string', 'max:255'],
            'broker_id' => ['nullable', 'integer'],
            'new_broker_name' => ['nullable', 'string', 'max:255'],
            'new_broker_phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'seller_name' => ['nullable', 'string', 'max:255'],
            'seller_phone' => ['nullable', 'string', 'max:30'],
            'purchase_price' => ['required', 'numeric', 'min:0.01'],
            'buyer_name' => ['nullable', 'string', 'max:255'],
            'buyer_phone' => ['nullable', 'string', 'max:30'],
            'sale_price' => ['nullable', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:open,sold,cancelled'],
            'deal_date' => ['nullable', 'date'],
            'sold_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
