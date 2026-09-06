<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::orderBy('sort_order')->get();

        return view('admin.pricing.index', compact('plans'));
    }

    public function edit(PricingPlan $plan)
    {
        return view('admin.pricing.edit', compact('plan'));
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
