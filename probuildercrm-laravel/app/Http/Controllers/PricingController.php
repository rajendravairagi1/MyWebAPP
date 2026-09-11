<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Support\Currency;
use App\Support\Geo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PricingController extends Controller
{
    public function index(Request $request)
    {
        $plans = PricingPlan::orderBy('sort_order')->get();
        $discountPercent = (int) SiteSetting::get('pricing_discount_percent', '40');
        $currency = Geo::currencyForRequest($request);
        $currencySymbol = Currency::symbol($currency);
        $availableCurrencies = Currency::SYMBOLS;

        return view('pricing', compact('plans', 'discountPercent', 'currency', 'currencySymbol', 'availableCurrencies'));
    }

    /**
     * The pricing page's currency dropdown — an explicit manual choice,
     * stored in a cookie so it sticks for the visitor's whole session
     * (and overrides geo-detection, see Geo::currencyForRequest()).
     */
    public function setCurrency(Request $request): RedirectResponse
    {
        $currency = $request->string('currency')->upper()->toString();

        if (! Currency::isSupported($currency)) {
            return back();
        }

        return back()->withCookie(cookie(Geo::COOKIE, $currency, 60 * 24 * 365));
    }
}
