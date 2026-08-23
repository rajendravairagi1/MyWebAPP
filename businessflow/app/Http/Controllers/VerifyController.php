<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\View\View;

class VerifyController extends Controller
{
    public function quotation(Quotation $quotation): View
    {
        $quotation->load('customer');
        $business = \App\Models\Business::find($quotation->business_id);

        return view('verify.show', [
            'business' => $business,
            'docType' => 'Quotation',
            'docNumber' => $quotation->number,
            'customerName' => $quotation->customer->name,
            'amount' => $quotation->total,
            'date' => $quotation->created_at,
            'status' => $quotation->status,
        ]);
    }

    public function invoice(Invoice $invoice): View
    {
        $invoice->load('customer');
        $business = \App\Models\Business::find($invoice->business_id);

        return view('verify.show', [
            'business' => $business,
            'docType' => 'Invoice',
            'docNumber' => $invoice->number,
            'customerName' => $invoice->customer->name,
            'amount' => $invoice->total,
            'date' => $invoice->created_at,
            'status' => $invoice->status,
        ]);
    }

    public function customer(Customer $customer): View
    {
        $customer->load('invoices');
        $business = \App\Models\Business::find($customer->business_id);

        $totalInvoiced = $customer->invoices->sum('total');
        $totalPaid = $customer->invoices->sum('amount_paid');

        return view('verify.show', [
            'business' => $business,
            'docType' => 'Statement',
            'docNumber' => 'Customer #'.$customer->id,
            'customerName' => $customer->name,
            'amount' => max(0, $totalInvoiced - $totalPaid),
            'amountLabel' => 'Balance due (as of now)',
            'date' => now(),
            'status' => null,
        ]);
    }
}
