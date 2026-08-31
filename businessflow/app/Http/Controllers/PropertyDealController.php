<?php

namespace App\Http\Controllers;

use App\Models\PropertyDeal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyDealController extends Controller
{
    public function index(): View
    {
        $deals = PropertyDeal::orderByDesc('deal_date')->orderByDesc('id')->get();

        $sold = $deals->where('status', 'sold');

        $totals = [
            'count' => $deals->count(),
            'open' => $deals->where('status', 'open')->count(),
            'total_purchase' => (float) $sold->sum('purchase_price'),
            'total_sale' => (float) $sold->sum('sale_price'),
            'total_profit' => (float) $sold->sum(fn (PropertyDeal $d) => $d->profit()),
        ];

        return view('property-deals.index', compact('deals', 'totals'));
    }

    public function store(Request $request): RedirectResponse
    {
        PropertyDeal::create($this->validated($request));

        return back()->with('status', 'Deal added.');
    }

    public function update(Request $request, PropertyDeal $deal): RedirectResponse
    {
        $data = $this->validated($request);

        // Selling for the first time (or re-marking sold) without an
        // explicit date defaults to today, same as elsewhere in the app.
        if ($data['status'] === 'sold' && ! $deal->sold_date && empty($data['sold_date'])) {
            $data['sold_date'] = now()->toDateString();
        }

        $deal->update($data);

        return back()->with('status', 'Deal updated.');
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
