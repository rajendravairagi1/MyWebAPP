<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\SiteSetting;

class PricingController extends Controller
{
    public function index()
    {
        $plans = PricingPlan::orderBy('sort_order')->get();
        $discountPercent = (int) SiteSetting::get('pricing_discount_percent', '40');

        return view('pricing', compact('plans', 'discountPercent'));
    }
}
