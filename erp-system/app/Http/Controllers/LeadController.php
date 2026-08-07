<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Lead::with('assignedUser')->latest();

        if (Auth::user()->hasRole('Business Developer') && ! Auth::user()->hasRole('Owner')) {
            $query->where('assigned_to', Auth::id());
        }

        $leads = $query->when($status, fn ($q) => $q->where('status', $status))
            ->paginate(20)
            ->withQueryString();

        return view('leads.index', ['leads' => $leads, 'status' => $status]);
    }

    public function create()
    {
        $users = User::orderBy('name')->get();

        return view('leads.create', ['users' => $users]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $validated['assigned_to'] = $validated['assigned_to'] ?? Auth::id();

        $lead = Lead::create($validated);

        return redirect()->route('leads.show', $lead)->with('status', "{$lead->name} lead add ho gaya.");
    }

    public function show(Lead $lead)
    {
        $lead->load(['meetings', 'assignedUser']);

        return view('leads.show', ['lead' => $lead]);
    }

    public function edit(Lead $lead)
    {
        $users = User::orderBy('name')->get();

        return view('leads.edit', ['lead' => $lead, 'users' => $users]);
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $this->validated($request);

        $lead->update($validated);

        return redirect()->route('leads.show', $lead)->with('status', 'Lead update ho gaya.');
    }

    public function storeMeeting(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'meeting_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'next_follow_up_date' => ['nullable', 'date'],
        ]);

        $lead->meetings()->create($validated);

        return back()->with('status', 'Meeting note add ho gaya.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'source' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:new,contacted,qualified,converted,lost'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
