<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Support\Currency;
use App\Support\Geo;
use App\Support\GeoIpUpdater;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::orderBy('sort_order')->get();
        $discountPercent = (int) SiteSetting::get('pricing_discount_percent', '40');
        $geoIpInstalled = Geo::databaseInstalled();
        $geoIpUpdatedAt = $geoIpInstalled ? filemtime(Geo::databasePath()) : null;
        $maxmindKeyConfigured = (bool) config('services.maxmind.license_key');

        return view('admin.pricing.index', compact('plans', 'discountPercent', 'geoIpInstalled', 'geoIpUpdatedAt', 'maxmindKeyConfigured'));
    }

    /**
     * Downloads the latest MaxMind GeoLite2-Country database — the button
     * next to "GeoIP Database" in the pricing admin page calls this, so
     * keeping country detection current never needs SSH/cron access.
     */
    public function updateGeoIp(): RedirectResponse
    {
        try {
            GeoIpUpdater::update();
        } catch (\Throwable $e) {
            return redirect()->route('admin.pricing.index')->withErrors(['geoip' => $e->getMessage()]);
        }

        return redirect()->route('admin.pricing.index')->with('status', 'GeoIP database updated — country-based pricing is now live.');
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
        $otherCurrencies = collect(Currency::SYMBOLS)->except(Currency::DEFAULT);

        return view('admin.pricing.edit', compact('plan', 'discountPercent', 'otherCurrencies'));
    }

    public function update(Request $request, PricingPlan $plan)
    {
        $otherCurrencies = array_keys(Currency::SYMBOLS);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'monthly_price' => 'required|integer|min:0',
            'features' => 'nullable|string',
            'extra_prices' => 'nullable|array',
            'extra_prices.*' => 'nullable|integer|min:0',
        ]);

        $features = collect(preg_split('/\r\n|\r|\n/', (string) $validated['features']))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        // Only currencies actually left filled in are stored — an emptied
        // field means "fall back to the INR price for this currency again",
        // not "charge zero".
        $extraPrices = collect($validated['extra_prices'] ?? [])
            ->only($otherCurrencies)
            ->filter(fn ($price) => filled($price))
            ->map(fn ($price) => (int) $price)
            ->all();

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'monthly_price' => $validated['monthly_price'],
            'extra_prices' => $extraPrices,
            'features' => $features,
        ]);

        return redirect()->route('admin.pricing.index')->with('status', 'Plan updated.');
    }
}
