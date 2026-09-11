<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;

class LeadsController extends Controller
{
    public function index()
    {
        $leads = ContactSubmission::orderByDesc('created_at')->paginate(25);

        // Snapshot which of these were unread before this visit clears
        // the sidebar badge, so the page can still show a "New" tag on
        // them this one time.
        $unreadIds = $leads->getCollection()->whereNull('read_at')->pluck('id');

        ContactSubmission::whereNull('read_at')->update(['read_at' => now()]);

        return view('admin.leads.index', compact('leads', 'unreadIds'));
    }

    public function destroy(ContactSubmission $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }
}
