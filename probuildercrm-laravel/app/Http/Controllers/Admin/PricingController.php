<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::orderBy('sort_order')->get();
        $discountPercent = (int) SiteSetting::get('pricing_discount_percent', '40');

        return view('admin.pricing.index', compact('plans', 'discountPercent'));
    }

    public function updateDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_percent' => 'required|integer|min:0|max:90',
        ]);

        SiteSetting::set('pricing_discount_percent', (string) $validated['discount_percent']);

        return redirect()->route('admin.pricing.index')->with('status', 'Offer updated - it now shows everywhere pricing is displayed.');
    }

    public function edit(PricingPlan $plan)
    {
        $discountPercent = (int) SiteSetting::get('pricing_discount_percent', '40');

        return view('admin.pricing.edit', compact('plan', 'discountPercent'));
    }

    public function update(Request $request, PricingPlan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'monthly_price' => 'required|integer|min:0',
            'features' => 'nullable|string',
        ]);

        $features = collect(preg_split('/\r\n|\r|\n/', (string) $validated['features']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'monthly_price' => $validated['monthly_price'],
            'features' => $features,
        ]);

        return redirect()->route('admin.pricing.index')->with('status', 'Plan updated.');
    }
}
