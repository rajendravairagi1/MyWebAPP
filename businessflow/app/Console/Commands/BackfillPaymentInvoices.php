<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\UnitPayment;
use App\Support\Tenant;
use App\Support\UnitPaymentRecorder;
use Illuminate\Console\Command;

/**
 * Every payment recorded through the normal "Record Payment" flow
 * auto-generates its own receipt invoice (see UnitPaymentRecorder) —
 * but that only ever applied going forward. This catches up any
 * payment still missing one (recorded before that existed, or slipped
 * through some other way) by generating it now. Safe to run repeatedly
 * — a payment that already has a receipt invoice is left untouched, so
 * this is wired into every /migrate run rather than needing its own
 * one-off trigger.
 */
class BackfillPaymentInvoices extends Command
{
    protected $signature = 'payments:backfill-invoices';

    protected $description = "Generate the missing receipt invoice for any recorded payment that doesn't already have one.";

    public function handle(): int
    {
        $created = 0;

        foreach (Business::all() as $business) {
            Tenant::runAs($business->id, function () use (&$created) {
                UnitPayment::whereDoesntHave('invoice')
                    ->whereNotNull('customer_id')
                    ->with('unit')
                    ->get()
                    ->each(function (UnitPayment $payment) use (&$created) {
                        if (! $payment->unit) {
                            return;
                        }

                        UnitPaymentRecorder::syncReceiptInvoice($payment->unit, $payment);
                        $created++;
                    });
            });
        }

        $this->info("Generated {$created} missing receipt invoice(s).");

        return self::SUCCESS;
    }
}
