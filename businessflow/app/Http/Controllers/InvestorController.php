<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Investor;
use App\Models\InvestorTransaction;
use App\Models\Project;
use App\Support\DocumentQr;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class InvestorController extends Controller
{
    public function index(): View
    {
        $investors = Investor::withCount('transactions')->orderBy('name')->get();

        return view('investors.index', compact('investors'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this->validated($request);

        $investor = Investor::create($data);

        if ($request->wantsJson()) {
            return response()->json(['id' => $investor->id, 'name' => $investor->name]);
        }

        return redirect()->route('investors.show', $investor)->with('status', 'Investor added.');
    }

    public function show(Investor $investor): View
    {
        $investor->load('transactions.project');
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('investors.show', compact('investor', 'projects'));
    }

    public function update(Request $request, Investor $investor): RedirectResponse
    {
        $investor->update($this->validated($request));

        return back()->with('status', 'Investor updated.');
    }

    public function destroy(Investor $investor): RedirectResponse
    {
        $investor->delete();

        return redirect()->route('investors.index')->with('status', 'Investor deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    public function storeTransaction(Request $request, Investor $investor): RedirectResponse
    {
        $data = $this->validatedTransaction($request);

        $investor->transactions()->create($data + ['recorded_by' => auth()->id()]);

        return back()->with('status', (new InvestorTransaction($data))->typeLabel().' recorded.');
    }

    public function updateTransaction(Request $request, Investor $investor, InvestorTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->investor_id === $investor->id, 404);

        $transaction->update($this->validatedTransaction($request));

        return back()->with('status', 'Transaction updated.');
    }

    protected function validatedTransaction(Request $request): array
    {
        $data = $request->validate([
            'type' => ['required', 'in:investment,profit_credited,payment_paid'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_date' => ['required', 'date'],
            'project_id' => ['nullable', 'integer'],
            'method' => ['nullable', 'string', 'max:50'],
            'reference' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! empty($data['project_id'])) {
            Project::findOrFail($data['project_id']);
        }

        return $data;
    }

    public function destroyTransaction(Investor $investor, InvestorTransaction $transaction): RedirectResponse
    {
        abort_unless($transaction->investor_id === $investor->id, 404);

        $transaction->delete();

        return back()->with('status', 'Transaction removed.');
    }

    public function statement(Investor $investor)
    {
        $investor->load('transactions.project');
        $business = Business::find(Tenant::id());
        $verifyQr = DocumentQr::dataUri(
            URL::signedRoute('verify.investor', ['investor' => $investor->id])
        );

        return Pdf::loadView('investors.statement', compact('investor', 'business', 'verifyQr'))
            ->download('Statement - '.$investor->name.'.pdf');
    }
}
