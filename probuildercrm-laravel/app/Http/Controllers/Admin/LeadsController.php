<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;

class LeadsController extends Controller
{
    public function index()
    {
        $leads = ContactSubmission::orderByDesc('created_at')->paginate(25);

        return view('admin.leads.index', compact('leads'));
    }

    public function destroy(ContactSubmission $lead)
    {
        $lead->delete();

        return redirect()->route('admin.leads.index')->with('status', 'Lead deleted.');
    }
}
