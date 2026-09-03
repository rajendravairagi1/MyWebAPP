<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Contractor;
use App\Support\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ContractorController extends Controller
{
    public function index(): View
    {
        $contractors = Contractor::withCount('costs')->orderBy('name')->get();

        return view('contractors.index', [
            'contractors' => $contractors,
            'types' => Contractor::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $contractor = Contractor::create($this->validated($request));

        return redirect()->route('contractors.show', $contractor)->with('status', 'Contractor added.');
    }

    public function show(Contractor $contractor): View
    {
        $contractor->load('costs.project', 'costs.account');

        return view('contractors.show', compact('contractor'));
    }

    /**
     * Every payment ever recorded against this contractor, across every
     * project — a record you can hand or send them showing exactly what
     * they were paid and when.
     */
    public function statement(Contractor $contractor): Response
    {
        $contractor->load('costs.project', 'costs.account');
        $business = Business::find(Tenant::id());

        return Pdf::loadView('contractors.statement', compact('contractor', 'business'))
            ->download('Statement - '.$contractor->name.'.pdf');
    }

    public function update(Request $request, Contractor $contractor): RedirectResponse
    {
        $contractor->update($this->validated($request));

        return back()->with('status', 'Contractor updated.');
    }

    public function destroy(Contractor $contractor): RedirectResponse
    {
        abort_if($contractor->costs()->exists(), 422, "This contractor has payments recorded against them and can't be deleted.");

        $contractor->delete();

        return redirect()->route('contractors.index')->with('status', 'Contractor deleted.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:'.implode(',', array_keys(Contractor::TYPES))],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
