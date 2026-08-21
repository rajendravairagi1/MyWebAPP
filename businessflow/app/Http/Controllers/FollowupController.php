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
            'due_at' => ['required', 'date'],
        ]);

        Followup::create($data + ['owner_id' => auth()->id()]);

        return redirect()->route('followups.index')->with('status', 'Follow-up scheduled.');
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
