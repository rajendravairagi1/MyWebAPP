<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Job;
use App\Models\Purchase;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->get('from') ?: now()->startOfMonth()->toDateString();
        $to = $request->get('to') ?: now()->toDateString();

        $jobCounts = Job::whereBetween('received_date', [$from, $to])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $invoiceTotal = Invoice::whereBetween('invoice_date', [$from, $to])->sum('total');
        $paymentTotal = (float) DB::table('payments')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $purchaseTotal = Purchase::whereBetween('purchase_date', [$from, $to])->sum('amount');
        $purchasePaid = (float) DB::table('purchase_payments')
            ->whereBetween('paid_at', [$from, $to])
            ->sum('amount');

        $lowStockMaterials = RawMaterial::whereColumn('current_stock', '<=', 'low_stock_alert_at')->get();

        $outstandingReceivables = Invoice::where('status', '!=', 'paid')->get()
            ->sum(fn (Invoice $i) => $i->balanceDue());

        $outstandingPayables = Purchase::where('status', '!=', 'paid')->get()
            ->sum(fn (Purchase $p) => $p->balanceDue());

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'jobCounts' => $jobCounts,
            'invoiceTotal' => $invoiceTotal,
            'paymentTotal' => $paymentTotal,
            'purchaseTotal' => $purchaseTotal,
            'purchasePaid' => $purchasePaid,
            'lowStockMaterials' => $lowStockMaterials,
            'outstandingReceivables' => $outstandingReceivables,
            'outstandingPayables' => $outstandingPayables,
        ]);
    }
}
