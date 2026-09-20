<?php

namespace App\Console\Commands;

use App\Models\Business;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Meeting;
use App\Support\PushNotifier;
use App\Support\Tenant;
use Illuminate\Console\Command;

/**
 * Sweeps every business for meetings/follow-ups starting soon and
 * invoices overdue for payment, and pushes a phone notification for
 * each one (App\Support\PushNotifier) that hasn't already been
 * reminded. Meant to run every few minutes — see routes/console.php
 * for the schedule, and RunNotificationsController for the no-cron
 * fallback (same pattern as backup:run / RunBackupsController).
 */
class SendDueNotifications extends Command
{
    protected $signature = 'notifications:send-due';

    protected $description = 'Push a phone notification for meetings/follow-ups starting soon and overdue invoices.';

    public function handle(): int
    {
        foreach (Business::all() as $business) {
            Tenant::runAs($business->id, function () {
                $this->remindMeetings();
                $this->remindFollowups();
                $this->remindInvoices();
            });
        }

        return self::SUCCESS;
    }

    private function remindMeetings(): void
    {
        Meeting::with('creator')
            ->whereNull('push_reminded_at')
            ->whereBetween('scheduled_at', [now(), now()->addMinutes(30)])
            ->get()
            ->each(function (Meeting $meeting) {
                if (! $meeting->creator) {
                    return;
                }

                PushNotifier::send(
                    $meeting->creator,
                    'Upcoming meeting: '.$meeting->title,
                    'At '.$meeting->scheduled_at->format('h:i A').($meeting->location ? ' · '.$meeting->location : ''),
                    route('meetings.index')
                );

                $meeting->update(['push_reminded_at' => now()]);
            });
    }

    private function remindFollowups(): void
    {
        Followup::with('owner')
            ->whereNull('push_reminded_at')
            ->where('status', '!=', 'done')
            ->whereBetween('due_at', [now(), now()->addMinutes(30)])
            ->get()
            ->each(function (Followup $followup) {
                if (! $followup->owner) {
                    return;
                }

                PushNotifier::send(
                    $followup->owner,
                    'Follow-up due: '.$followup->categoryLabel(),
                    $followup->note ?: 'Tap to view details.',
                    route('followups.index')
                );

                $followup->update(['push_reminded_at' => now()]);
            });
    }

    /**
     * Re-reminds once a day per overdue invoice (not just once ever) —
     * push_reminded_at is only checked against "before today", not left
     * permanently null-only, since an unpaid invoice staying overdue for
     * a week is still worth nudging every day.
     */
    private function remindInvoices(): void
    {
        Invoice::with('creator', 'customer')
            ->whereIn('status', ['sent', 'partially_paid', 'overdue'])
            ->whereNotNull('due_date')
            ->where('due_date', '<=', now()->toDateString())
            ->where(function ($q) {
                $q->whereNull('push_reminded_at')->orWhere('push_reminded_at', '<', now()->startOfDay());
            })
            ->get()
            ->filter(fn (Invoice $invoice) => $invoice->total > $invoice->amount_paid)
            ->each(function (Invoice $invoice) {
                if (! $invoice->creator) {
                    return;
                }

                $remaining = $invoice->total - $invoice->amount_paid;

                PushNotifier::send(
                    $invoice->creator,
                    'Payment overdue: Invoice #'.$invoice->number,
                    ($invoice->customer->name ?? 'Customer').' — '.\App\Support\Tenant::currencySymbol().number_format($remaining, 0).' pending',
                    route('invoices.show', $invoice)
                );

                $invoice->update(['push_reminded_at' => now()]);
            });
    }
}
