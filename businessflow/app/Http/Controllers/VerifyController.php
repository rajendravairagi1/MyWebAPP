<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Investor;
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
        $customer->load('units.payments');
        $business = \App\Models\Business::find($customer->business_id);

        $totalDue = $customer->units->sum(fn ($unit) => $unit->totalOutstanding());

        return view('verify.show', [
            'business' => $business,
            'docType' => 'Statement',
            'docNumber' => 'Customer #'.$customer->id,
            'customerName' => $customer->name,
            'amount' => $totalDue,
            'amountLabel' => 'Balance due (as of now)',
            'date' => now(),
            'status' => null,
        ]);
    }

    public function investor(Investor $investor): View
    {
        $business = \App\Models\Business::find($investor->business_id);

        return view('verify.show', [
            'business' => $business,
            'docType' => 'Statement',
            'docNumber' => 'Investor #'.$investor->id,
            'partyLabel' => 'Investor',
            'customerName' => $investor->name,
            'amount' => $investor->balance(),
            'amountLabel' => 'Balance (as of now)',
            'date' => now(),
            'status' => null,
        ]);
    }
}
