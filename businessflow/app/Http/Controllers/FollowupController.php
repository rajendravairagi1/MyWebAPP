<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Followup;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowupController extends Controller
{
    public function index(): View
    {
        $followups = Followup::with(['customer', 'project'])
            ->where('status', 'pending')
            ->orderBy('due_at')
            ->get();

        $overdue = $followups->filter(fn ($f) => $f->due_at->isPast());
        $upcoming = $followups->filter(fn ($f) => ! $f->due_at->isPast());

        return view('followups.index', compact('overdue', 'upcoming'));
    }

    public function create(): View
    {
        return view('followups.create', [
            'customers' => Customer::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'note' => ['required', 'string', 'max:1000'],
            'category' => ['nullable', 'in:general,installment,registry,site_visit,documentation,other'],
            'category_other' => ['nullable', 'string', 'max:100'],
            'due_at' => ['required', 'date'],
        ]);

        $data['category'] = $data['category'] ?? 'general';
        if ($data['category'] === 'other' && filled($data['category_other'] ?? null)) {
            $data['category'] = $data['category_other'];
        }
        unset($data['category_other']);

        Followup::create($data + ['owner_id' => auth()->id()]);

        return redirect()->route('customers.show', $data['customer_id'])->with('status', 'Follow-up scheduled.');
    }

    public function complete(Followup $followup): RedirectResponse
    {
        $followup->update(['status' => 'done']);

        return back()->with('status', 'Marked as done.');
    }

    public function destroy(Followup $followup): RedirectResponse
    {
        $followup->delete();

        return back()->with('status', 'Follow-up removed.');
    }
}
