<?php

namespace App\Http\Controllers;

use App\Models\BrokerTransaction;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectUnit;
use App\Models\PropertyDeal;
use App\Support\Tenant;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $business = Business::find(Tenant::id());
        $smartAlertsEnabled = $business?->smart_alerts_enabled ?? true;
        $paymentRemindersEnabled = $business?->payment_reminders_enabled ?? true;

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

        // Revenue trend: sales raised each of the last 6 months (this
        // month included), oldest first — a quick shape of the business
        // rather than the single "this month" number shown elsewhere.
        $revenueTrendLabels = [];
        $revenueTrendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueTrendLabels[] = $month->format('M');
            $revenueTrendData[] = (float) Invoice::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total');
        }

        // Invoice status breakdown — fixed label order so the chart's
        // legend/colors stay consistent regardless of which statuses
        // actually have invoices right now.
        $invoiceStatusCounts = Invoice::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $invoiceStatusMeta = [
            'paid' => ['label' => __('Paid'), 'color' => '#22c55e'],
            'sent' => ['label' => __('Sent'), 'color' => '#3b82f6'],
            'partially_paid' => ['label' => __('Partially Paid'), 'color' => '#f59e0b'],
            'overdue' => ['label' => __('Overdue'), 'color' => '#ef4444'],
            'draft' => ['label' => __('Draft'), 'color' => '#9ca3af'],
        ];
        $invoiceStatusLabels = [];
        $invoiceStatusData = [];
        $invoiceStatusColors = [];
        foreach ($invoiceStatusMeta as $key => $meta) {
            $count = (int) ($invoiceStatusCounts[$key] ?? 0);
            if ($count > 0) {
                $invoiceStatusLabels[] = $meta['label'];
                $invoiceStatusData[] = $count;
                $invoiceStatusColors[] = $meta['color'];
            }
        }

        // Unit booking status across every (non-archived) unit in every
        // project — a portfolio-wide occupancy snapshot.
        $unitStatusCounts = ProjectUnit::whereNull('archived_at')
            ->selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        $unitStatusMeta = [
            'available' => ['label' => __('Available'), 'color' => '#3b82f6'],
            'booked' => ['label' => __('Booked'), 'color' => '#f59e0b'],
            'sold' => ['label' => __('Sold'), 'color' => '#22c55e'],
        ];
        $unitStatusLabels = [];
        $unitStatusData = [];
        $unitStatusColors = [];
        foreach ($unitStatusMeta as $key => $meta) {
            $count = (int) ($unitStatusCounts[$key] ?? 0);
            if ($count > 0) {
                $unitStatusLabels[] = $meta['label'];
                $unitStatusData[] = $count;
                $unitStatusColors[] = $meta['color'];
            }
        }

        // Top 5 projects by profit (revenue minus cost) — reuses the same
        // per-project figures already computed for the portfolio chart.
        $topProjects = $projects->sortByDesc(fn ($p) => $p->profit())->take(5)->values();
        $topProjectsLabels = $topProjects->pluck('name')->all();
        $topProjectsData = $topProjects->map(fn ($p) => round($p->profit(), 2))->all();
        $topProjectsColors = $topProjects->map(fn ($p) => $p->profit() >= 0 ? '#22c55e' : '#ef4444')->all();

        // How much of everything ever invoiced has actually been collected.
        $totalInvoiced = (float) Invoice::sum('total');
        $totalCollected = (float) Invoice::sum('amount_paid');
        $collectionRate = $totalInvoiced > 0 ? round($totalCollected / $totalInvoiced * 100, 1) : 0;

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
        if ($smartAlertsEnabled) {
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
        } else {
            $staleCustomers = collect();
            $staleCustomersCount = 0;
            $staleBookedUnits = collect();
            $staleBookedUnitsCount = 0;
        }

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
            'paymentRemindersEnabled' => $paymentRemindersEnabled,
            'revenueTrendLabels' => $revenueTrendLabels,
            'revenueTrendData' => $revenueTrendData,
            'invoiceStatusLabels' => $invoiceStatusLabels,
            'invoiceStatusData' => $invoiceStatusData,
            'invoiceStatusColors' => $invoiceStatusColors,
            'unitStatusLabels' => $unitStatusLabels,
            'unitStatusData' => $unitStatusData,
            'unitStatusColors' => $unitStatusColors,
            'topProjectsLabels' => $topProjectsLabels,
            'topProjectsData' => $topProjectsData,
            'topProjectsColors' => $topProjectsColors,
            'totalInvoiced' => $totalInvoiced,
            'totalCollected' => $totalCollected,
            'collectionRate' => $collectionRate,
        ]);
    }
}
