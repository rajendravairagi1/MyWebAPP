<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'solo',
                'name' => 'Solo',
                'description' => 'For one builder managing their own projects, start to finish.',
                'monthly_price' => 999,
                'highlighted' => false,
                'sort_order' => 1,
                'features' => [
                    'Unlimited projects & units',
                    'Customer bookings & payments',
                    'Quotations & invoices',
                    'Property brochure sharing',
                    'Ledger & reports',
                ],
            ],
            [
                'slug' => 'team',
                'name' => 'Builder Team',
                'description' => 'For a builder with supervisors, sales staff or site managers.',
                'monthly_price' => 2499,
                'highlighted' => true,
                'sort_order' => 2,
                'features' => [
                    'Everything in Solo',
                    'Team members with role-based access',
                    'Contractor & vendor ledgers',
                    'Broker commission tracking',
                    'Investor accounts',
                    'Loan disbursement tracking',
                ],
            ],
            [
                'slug' => 'company',
                'name' => 'Company',
                'description' => 'For a company running multiple branches or cities.',
                'monthly_price' => 4999,
                'highlighted' => false,
                'sort_order' => 3,
                'features' => [
                    'Everything in Builder Team',
                    'Multiple branches under one account',
                    'Combined company-wide dashboard',
                    'Priority support',
                ],
            ],
        ];

        // firstOrCreate, not updateOrCreate — this seeder also runs on every
        // /migrate after a deploy, and must never stomp a price the admin
        // has already changed via /admin/pricing.
        foreach ($plans as $plan) {
            PricingPlan::firstOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
