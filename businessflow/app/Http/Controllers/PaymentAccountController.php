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
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
