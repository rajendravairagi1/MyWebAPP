<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Invoice;
use App\Support\Tenant;
use Illuminate\View\View;

class PaymentReminderController extends Controller
{
    public function index(): View
    {
        abort_unless(Business::find(Tenant::id())?->payment_reminders_enabled ?? true, 404);

        $unpaidInvoices = Invoice::with('customer')
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->get()
            ->filter(fn (Invoice $invoice) => $invoice->total > $invoice->amount_paid);

        $overdueInvoices = $unpaidInvoices
            ->filter(fn (Invoice $invoice) => $invoice->due_date && $invoice->due_date->isPast())
            ->sortBy('due_date')
            ->values();

        $dueSoonInvoices = $unpaidInvoices
            ->reject(fn (Invoice $invoice) => $invoice->due_date && $invoice->due_date->isPast())
            ->sortBy('due_date')
            ->values();

        return view('payment-reminders.index', compact('overdueInvoices', 'dueSoonInvoices'));
    }
}
