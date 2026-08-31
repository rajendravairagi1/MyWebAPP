<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\PropertyDeal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $unpaidInvoices = Invoice::whereIn('status', ['sent', 'partially_paid', 'overdue'])->get();
        $overdueInvoices = $unpaidInvoices->filter(fn ($invoice) => $invoice->due_date && $invoice->due_date->isPast());

        $salesThisMonth = Invoice::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $projects = Project::withCount('units')->get();

        // A sold resale deal is real cost-in/money-out for the business
        // just like a project, so it's folded into the same Cost/Received
        // figures here — Profit stays literally Received minus Cost.
        $soldDeals = PropertyDeal::where('status', 'sold')->get();
        $dealsOpenCount = PropertyDeal::where('status', 'open')->count();
        $dealsSoldCount = $soldDeals->count();
        $dealsCost = (float) $soldDeals->sum('purchase_price');
        $dealsRevenue = (float) $soldDeals->sum('sale_price');
        $dealsProfit = (float) $soldDeals->sum(fn (PropertyDeal $d) => $d->profit());

        $portfolioCost = (float) ProjectCost::sum('amount') + $dealsCost;
        $portfolioRevenue = (float) Invoice::whereNotNull('project_id')->sum('amount_paid') + $dealsRevenue;

        $dueFollowups = Followup::with('customer')
            ->where('status', 'pending')
            ->where('due_at', '<=', now()->addDay())
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'customerCount' => Customer::count(),
            'unpaidCount' => $unpaidInvoices->count(),
            'unpaidTotal' => $unpaidInvoices->sum(fn ($invoice) => $invoice->total - $invoice->amount_paid),
            'overdueCount' => $overdueInvoices->count(),
            'salesThisMonth' => $salesThisMonth,
            'recentInvoices' => Invoice::with('customer')->latest()->limit(5)->get(),
            'projects' => $projects,
            'projectCount' => $projects->count(),
            'ongoingProjectCount' => $projects->where('status', 'ongoing')->count(),
            'portfolioCost' => $portfolioCost,
            'portfolioRevenue' => $portfolioRevenue,
            'portfolioProfit' => $portfolioRevenue - $portfolioCost,
            'dealsOpenCount' => $dealsOpenCount,
            'dealsSoldCount' => $dealsSoldCount,
            'dealsProfit' => $dealsProfit,
            'dueFollowups' => $dueFollowups,
        ]);
    }
}
