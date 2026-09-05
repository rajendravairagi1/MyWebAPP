<?php

namespace App\Http\Controllers;

use App\Models\BrokerTransaction;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectUnit;
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

        // Customers nobody has a plan for: no follow-up currently pending,
        // and no quotation/invoice raised recently either — these are the
        // leads quietly going cold that a due-date-based reminder alone
        // would never catch, since no follow-up was ever scheduled for
        // them in the first place.
        $staleCustomersQuery = Customer::whereDoesntHave('followups', fn ($q) => $q->where('status', 'pending'))
            ->whereDoesntHave('quotations', fn ($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->whereDoesntHave('invoices', fn ($q) => $q->where('created_at', '>=', now()->subDays(7)))
            ->where('created_at', '<=', now()->subDays(7));
        $staleCustomers = (clone $staleCustomersQuery)->latest()->limit(5)->get();
        $staleCustomersCount = $staleCustomersQuery->count();

        // Units marked "booked" (a customer assigned) but sitting with no
        // money collected at all — booking a unit doesn't require an
        // upfront payment, so this is the only way to catch a booking
        // that's stalled before it ever became a real sale.
        $staleBookedUnitsQuery = ProjectUnit::with(['project', 'customer'])
            ->whereNull('archived_at')
            ->where('status', 'booked')
            ->where('updated_at', '<=', now()->subDays(14))
            ->whereDoesntHave('payments')
            ->whereDoesntHave('invoices', fn ($q) => $q->whereHas('payments'));
        $staleBookedUnits = (clone $staleBookedUnitsQuery)->oldest('updated_at')->limit(5)->get();
        $staleBookedUnitsCount = $staleBookedUnitsQuery->count();

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
            'staleCustomers' => $staleCustomers,
            'staleCustomersCount' => $staleCustomersCount,
            'staleBookedUnits' => $staleBookedUnits,
            'staleBookedUnitsCount' => $staleBookedUnitsCount,
        ]);
    }
}
