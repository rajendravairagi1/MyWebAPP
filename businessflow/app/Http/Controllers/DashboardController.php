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
        $portfolioCost = ProjectCost::sum('amount');
        $portfolioRevenue = Invoice::whereNotNull('project_id')->sum('amount_paid');

        $dueFollowups = Followup::with('customer')
            ->where('status', 'pending')
            ->where('due_at', '<=', now()->addDay())
            ->orderBy('due_at')
            ->limit(5)
            ->get();

        // Resale/trading deals are a separate line of business from the
        // builder's own construction projects, so they get their own
        // card here rather than being folded into "Portfolio — all
        // projects" (which would make Cost/Received no longer add up to
        // the Profit shown, since a deal has no ProjectCost/Invoice rows).
        $soldDeals = PropertyDeal::where('status', 'sold')->get();
        $dealsOpenCount = PropertyDeal::where('status', 'open')->count();
        $dealsSoldCount = $soldDeals->count();
        $dealsProfit = (float) $soldDeals->sum(fn (PropertyDeal $d) => $d->profit());

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
