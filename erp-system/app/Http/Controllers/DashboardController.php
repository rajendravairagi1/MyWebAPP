<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('module.dashboard-owner')) {
            return $this->ownerDashboard();
        }

        return view('dashboard-general', [
            'todayReceived' => Job::whereDate('received_date', today())->count(),
            'myPendingJobs' => Job::whereIn('status', ['pending', 'working'])->count(),
        ]);
    }

    protected function ownerDashboard()
    {
        $totalInvoiced = Invoice::sum('total');
        $totalReceived = (float) DB::table('payments')->sum('amount');
        $totalPending = $totalInvoiced - $totalReceived;

        $jobCounts = Job::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $topCustomers = Customer::select('customers.id', 'customers.name')
            ->selectRaw('COALESCE(SUM(invoices.total), 0) as total_business')
            ->leftJoin('invoices', 'invoices.customer_id', '=', 'customers.id')
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_business')
            ->limit(5)
            ->get();

        $overdueInvoices = Invoice::with('customer')
            ->where('status', '!=', 'paid')
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $overdueJobs = Job::with('customer')
            ->whereIn('status', ['pending', 'working'])
            ->whereNotNull('due_date')
            ->where('due_date', '<', today())
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        $lowStockMaterials = RawMaterial::whereColumn('current_stock', '<=', 'low_stock_alert_at')->get();

        $recentJobs = Job::with('customer')->latest('received_date')->limit(8)->get();

        return view('dashboard-owner', [
            'totalInvoiced' => $totalInvoiced,
            'totalReceived' => $totalReceived,
            'totalPending' => $totalPending,
            'jobCounts' => $jobCounts,
            'topCustomers' => $topCustomers,
            'overdueInvoices' => $overdueInvoices,
            'overdueJobs' => $overdueJobs,
            'lowStockMaterials' => $lowStockMaterials,
            'recentJobs' => $recentJobs,
            'totalCustomers' => Customer::count(),
        ]);
    }
}
