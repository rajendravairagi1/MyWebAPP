<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\PaymentAccount;
use App\Models\UnitPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentAccountController extends Controller
{
    public function index(): View
    {
        $accounts = PaymentAccount::orderBy('name')->get();

        return view('payment-accounts.index', compact('accounts'));
    }

    /**
     * Every transaction recorded against this account/person, from all
     * three places money can be tagged with one — unit payments, generic
     * invoice payments, and manual ledger entries — combined into one
     * dated statement with a running in/out/balance total. This is the
     * only place those three sources get merged like this.
     */
    public function show(PaymentAccount $account): View
    {
        $rows = collect();

        UnitPayment::with(['unit.project', 'customer' => fn ($q) => $q->withTrashed()])
            ->where('payment_account_id', $account->id)
            ->get()
            ->each(function (UnitPayment $payment) use ($rows) {
                $rows->push((object) [
                    'date' => $payment->paid_at,
                    'direction' => 'in',
                    'amount' => (float) $payment->amount,
                    'description' => $payment->loan_id ? 'Bank loan disbursement' : $payment->purposeLabel(),
                    'context' => trim(($payment->unit?->project?->name ?? '').' · '.($payment->unit?->unit_number ?? ''), ' ·'),
                    'party' => $payment->customer?->name,
                    'link' => $payment->customer_id ? route('customers.show', $payment->customer_id) : null,
                ]);
            });

        Payment::with(['invoice.customer' => fn ($q) => $q->withTrashed()])
            ->where('payment_account_id', $account->id)
            ->get()
            ->each(function (Payment $payment) use ($rows) {
                $rows->push((object) [
                    'date' => $payment->paid_at,
                    'direction' => 'in',
                    'amount' => (float) $payment->amount,
                    'description' => 'Invoice payment',
                    'context' => $payment->invoice?->number,
                    'party' => $payment->invoice?->customer?->name,
                    'link' => $payment->invoice_id ? route('invoices.show', $payment->invoice_id) : null,
                ]);
            });

        LedgerEntry::with(['customer' => fn ($q) => $q->withTrashed(), 'project'])
            ->where('payment_account_id', $account->id)
            ->get()
            ->each(function (LedgerEntry $entry) use ($rows) {
                $rows->push((object) [
                    'date' => $entry->entry_date,
                    'direction' => $entry->type === 'income' ? 'in' : 'out',
                    'amount' => (float) $entry->amount,
                    'description' => $entry->description,
                    'context' => $entry->project?->name ?? $entry->category,
                    'party' => $entry->customer?->name,
                    'link' => $entry->project_id ? route('projects.show', $entry->project_id) : null,
                ]);
            });

        $rows = $rows->sortByDesc('date')->values();

        $totalIn = (float) $rows->where('direction', 'in')->sum('amount');
        $totalOut = (float) $rows->where('direction', 'out')->sum('amount');

        return view('payment-accounts.show', [
            'account' => $account,
            'rows' => $rows,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'balance' => $totalIn - $totalOut,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        PaymentAccount::create($this->validated($request));

        return back()->with('status', 'Account added.');
    }

    public function update(Request $request, PaymentAccount $account): RedirectResponse
    {
        $account->update($this->validated($request));

        return back()->with('status', 'Account updated.');
    }

    /**
     * Blocked once it's actually been used on a payment — this list
     * exists so you can prove later which account received/paid what
     * (e.g. for ITR), so a used account can't quietly lose that trail.
     * Rename it instead if it was set up wrong.
     */
    public function destroy(PaymentAccount $account): RedirectResponse
    {
        $inUse = UnitPayment::where('payment_account_id', $account->id)->exists()
            || Payment::where('payment_account_id', $account->id)->exists()
            || LedgerEntry::where('payment_account_id', $account->id)->exists();

        abort_if($inUse, 422, 'This account has payments recorded against it — rename it instead of deleting, so those records keep their trail.');

        $account->delete();

        return back()->with('status', 'Account removed.');
    }

    protected function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:bank,cash'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        // A cash-in-hand "account" is just a person — bank details
        // never apply to it, even if the form happened to submit some.
        if ($data['type'] === 'cash') {
            $data['bank_name'] = null;
            $data['account_number'] = null;
        }

        return $data;
    }
}
