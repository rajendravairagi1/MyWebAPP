<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectUnit;
use App\Models\UnitPayment;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * One shared data builder per report type feeds both the on-screen
 * preview and the PDF/CSV download, so what you see is always exactly
 * what you get in the file.
 */
class ReportController extends Controller
{
    private const TYPES = ['sales', 'collections', 'customers', 'projects'];

    public function index(Request $request): View
    {
        $type = in_array($request->query('type'), self::TYPES, true) ? $request->query('type') : 'sales';
        [$from, $to] = $this->dateRange($request);

        $report = $this->buildReport($type, $from, $to);

        return view('reports.index', [
            'type' => $type,
            'from' => $from,
            'to' => $to,
            'report' => $report,
        ]);
    }

    public function download(Request $request): Response|StreamedResponse
    {
        $type = in_array($request->query('type'), self::TYPES, true) ? $request->query('type') : 'sales';
        $format = $request->query('format') === 'csv' ? 'csv' : 'pdf';
        [$from, $to] = $this->dateRange($request);

        $report = $this->buildReport($type, $from, $to);
        $filename = $type.'-report-'.now()->format('Y-m-d');

        if ($format === 'csv') {
            return $this->downloadCsv($report, $filename);
        }

        $business = Business::find(Tenant::id());

        return Pdf::loadView('reports.pdf', ['report' => $report, 'business' => $business])
            ->download($filename.'.pdf');
    }

    /**
     * @return array{0: \Illuminate\Support\Carbon, 1: \Illuminate\Support\Carbon}
     */
    private function dateRange(Request $request): array
    {
        $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : now()->startOfMonth();
        $to = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }

    /**
     * @return array{title: string, dated: bool, columns: array<int, string>, rows: array<int, array<int, string>>, totals: array<string, string>}
     */
    private function buildReport(string $type, Carbon $from, Carbon $to): array
    {
        return match ($type) {
            'sales' => $this->salesReport($from, $to),
            'collections' => $this->collectionsReport($from, $to),
            'customers' => $this->customersReport(),
            'projects' => $this->projectsReport(),
        };
    }

    private function salesReport(Carbon $from, Carbon $to): array
    {
        $invoices = Invoice::with(['customer', 'project'])
            ->whereBetween('created_at', [$from, $to])
            ->orderBy('created_at')
            ->get();

        $rows = $invoices->map(fn (Invoice $inv) => [
            $inv->created_at->format('d M Y'),
            $inv->number,
            $inv->customer->name,
            $inv->project?->name ?? '—',
            number_format($inv->total, 2),
            number_format($inv->amount_paid, 2),
            ucfirst(str_replace('_', ' ', $inv->status)),
        ]);

        return [
            'title' => 'Sales Report',
            'dated' => true,
            'columns' => ['Date', 'Invoice #', 'Customer', 'Project', 'Total', 'Collected', 'Status'],
            'rows' => $rows->toArray(),
            'totals' => [
                'Invoices' => (string) $invoices->count(),
                'Total invoiced' => Tenant::currencySymbol().number_format($invoices->sum('total'), 2),
                'Total collected' => Tenant::currencySymbol().number_format($invoices->sum('amount_paid'), 2),
            ],
        ];
    }

    private function collectionsReport(Carbon $from, Carbon $to): array
    {
        $unitPayments = UnitPayment::with(['customer', 'unit.project'])
            ->whereBetween('paid_at', [$from, $to])
            ->get()
            ->map(fn (UnitPayment $p) => [
                'date' => $p->paid_at,
                'customer' => $p->customer?->name ?? '—',
                'context' => $p->unit?->project?->name ?? '—',
                'type' => 'Sale payment ('.($p->purpose ?: 'installment').')',
                'amount' => (float) $p->amount,
                'method' => $p->method ?? '—',
            ]);

        $invoicePayments = Payment::with(['invoice.customer'])
            ->whereBetween('paid_at', [$from, $to])
            ->get()
            ->map(fn (Payment $p) => [
                'date' => $p->paid_at,
                'customer' => $p->invoice?->customer?->name ?? '—',
                'context' => $p->invoice?->number ?? '—',
                'type' => 'Invoice payment',
                'amount' => (float) $p->amount,
                'method' => $p->method ?? '—',
            ]);

        $all = $unitPayments->concat($invoicePayments)->sortBy('date')->values();

        $rows = $all->map(fn ($r) => [
            $r['date']->format('d M Y'),
            $r['customer'],
            $r['context'],
            $r['type'],
            number_format($r['amount'], 2),
            ucfirst($r['method']),
        ]);

        return [
            'title' => 'Collections Report',
            'dated' => true,
            'columns' => ['Date', 'Customer', 'Project / Invoice', 'Type', 'Amount', 'Method'],
            'rows' => $rows->toArray(),
            'totals' => [
                'Payments' => (string) $all->count(),
                'Total collected' => Tenant::currencySymbol().number_format($all->sum('amount'), 2),
            ],
        ];
    }

    private function customersReport(): array
    {
        $customers = Customer::orderBy('name')->get();
        $units = ProjectUnit::with('customer')->whereNotNull('customer_id')->whereNull('archived_at')->get()->groupBy('customer_id');

        $rows = $customers->map(function (Customer $c) use ($units) {
            $custUnits = $units->get($c->id, collect());

            return [
                $c->name,
                $c->phone ?: '—',
                (string) $custUnits->count(),
                number_format($custUnits->sum('price'), 2),
                number_format($custUnits->sum(fn (ProjectUnit $u) => $u->totalCollected()), 2),
                number_format($custUnits->sum(fn (ProjectUnit $u) => $u->totalOutstanding()), 2),
            ];
        });

        return [
            'title' => 'Customers Report',
            'dated' => false,
            'columns' => ['Customer', 'Phone', 'Properties', 'Total Value', 'Collected', 'Outstanding'],
            'rows' => $rows->toArray(),
            'totals' => [
                'Customers' => (string) $customers->count(),
            ],
        ];
    }

    private function projectsReport(): array
    {
        $projects = Project::all();

        $rows = $projects->map(fn (Project $p) => [
            $p->name,
            ucfirst($p->status),
            number_format($p->totalCost(), 2),
            number_format($p->totalRevenue(), 2),
            number_format($p->profit(), 2),
        ]);

        return [
            'title' => 'Projects Report',
            'dated' => false,
            'columns' => ['Project', 'Status', 'Cost', 'Revenue', 'Profit / Loss'],
            'rows' => $rows->toArray(),
            'totals' => [
                'Projects' => (string) $projects->count(),
                'Total profit / loss' => Tenant::currencySymbol().number_format($projects->sum(fn (Project $p) => $p->profit()), 2),
            ],
        ];
    }

    private function downloadCsv(array $report, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($report) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $report['columns']);
            foreach ($report['rows'] as $row) {
                fputcsv($out, $row);
            }
            fputcsv($out, []);
            foreach ($report['totals'] as $label => $value) {
                fputcsv($out, [$label, $value]);
            }
            fclose($out);
        }, $filename.'.csv', ['Content-Type' => 'text/csv']);
    }
}
