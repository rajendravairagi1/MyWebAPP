<?php

namespace App\Http\Controllers;

use App\Models\BrokerTransaction;
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

        // A resale deal's property was never the business's own — only
        // the margin it earned belongs on this P&L, not the underlying
        // purchase/sale amounts (which would inflate Cost/Received for
        // money that was never really the business's own to begin with).
        $soldDeals = PropertyDeal::where('status', 'sold')->get();
        $dealsOpenCount = PropertyDeal::where('status', 'open')->count();
        $dealsSoldCount = $soldDeals->count();
        $dealsProfit = (float) $soldDeals->sum(fn (PropertyDeal $d) => $d->profit());

        $portfolioCost = (float) ProjectCost::sum('amount');
        $portfolioRevenue = (float) Invoice::whereNotNull('project_id')->sum('amount_paid');

        // Only what's actually been paid out to a broker counts against
        // profit — commission accrued but not yet paid isn't money that's
        // left the business, same cash-basis logic used everywhere else
        // on this page.
        $brokerCommissionPaid = (float) BrokerTransaction::where('type', 'payment_paid')->sum('amount');

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
            'portfolioProfit' => $portfolioRevenue - $portfolioCost + $dealsProfit - $brokerCommissionPaid,
            'dealsOpenCount' => $dealsOpenCount,
            'dealsSoldCount' => $dealsSoldCount,
            'dealsProfit' => $dealsProfit,
            'brokerCommissionPaid' => $brokerCommissionPaid,
            'dueFollowups' => $dueFollowups,
        ]);
    }
}
