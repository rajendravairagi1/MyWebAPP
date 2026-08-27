<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Meeting;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function index(): View
    {
        $upcoming = Meeting::with(['customer', 'project'])
            ->where('status', 'scheduled')
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Meeting $m) => $m->scheduled_at->toDateString());

        $past = Meeting::with(['customer', 'project'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->orderByDesc('scheduled_at')
            ->limit(20)
            ->get();

        return view('meetings.index', compact('upcoming', 'past'));
    }

    public function create(): View
    {
        return view('meetings.create', [
            'customers' => Customer::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'attendees' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['required', 'date'],
        ]);

        Meeting::create($data + ['created_by' => auth()->id()]);

        return redirect()->route('meetings.index')->with('status', 'Meeting scheduled.');
    }

    public function edit(Meeting $meeting): View
    {
        return view('meetings.edit', [
            'meeting' => $meeting,
            'customers' => Customer::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Meeting $meeting): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'location' => ['nullable', 'string', 'max:255'],
            'attendees' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['required', 'date'],
        ]);

        $meeting->update($data);

        return redirect()->route('meetings.index')->with('status', 'Meeting updated.');
    }

    public function complete(Meeting $meeting): RedirectResponse
    {
        $meeting->update(['status' => 'completed']);

        return back()->with('status', 'Marked as completed.');
    }

    public function cancel(Meeting $meeting): RedirectResponse
    {
        $meeting->update(['status' => 'cancelled']);

        return back()->with('status', 'Meeting cancelled.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $meeting->delete();

        return back()->with('status', 'Meeting removed.');
    }
}
