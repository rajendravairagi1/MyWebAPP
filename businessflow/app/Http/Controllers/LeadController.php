<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Lead;
use App\Support\DocumentQr;
use App\Support\LeadQrPoster;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function index(): View
    {
        $pending = Lead::where('status', Lead::STATUS_PENDING)->latest()->get();

        $active = Lead::whereNotIn('status', [Lead::STATUS_PENDING, Lead::REJECTED, Lead::LOST])
            ->whereNull('converted_customer_id')
            ->latest()
            ->get();

        $business = Business::find(Tenant::id());
        $publicUrl = route('leads.public.show-slug', $business->leadFormSlug());
        $posterUrl = route('leads.qr-poster');

        return view('leads.index', compact('pending', 'active', 'publicUrl', 'posterUrl'));
    }

    /**
     * The full branded, shareable "poster" image — logo, business name/
     * contact, the QR itself, app branding, the plain link, and a short
     * instruction — all composited server-side into one PNG (like a
     * PhonePe/BHIM QR card) so sharing or downloading it hands over one
     * complete image rather than a bare QR code.
     */
    public function qrPoster(): Response
    {
        $business = Business::find(Tenant::id());
        $publicUrl = route('leads.public.show-slug', $business->leadFormSlug());

        $png = LeadQrPoster::build($business, $publicUrl)
            ?? DocumentQr::png($publicUrl, 460);

        abort_if(! $png, 404);

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        // Entered directly by staff, so it skips the pending-approval queue
        // that public QR/link submissions go through.
        $lead = Lead::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'message' => $data['message'] ?? null,
            'source' => 'manual',
            'status' => 'new',
            'approved_at' => now(),
        ]);

        return redirect()->route('leads.show', $lead)->with('status', 'Lead added.');
    }

    public function show(Lead $lead): View
    {
        $lead->load(['followups' => fn ($q) => $q->orderByDesc('created_at')]);

        return view('leads.show', compact('lead'));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:'.implode(',', array_merge(array_keys(Lead::STAGES), [Lead::LOST]))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead->update($data);

        return back()->with('status', 'Lead updated.');
    }

    public function approve(Lead $lead): RedirectResponse
    {
        if (! $lead->isPending()) {
            return back();
        }

        $lead->update(['status' => 'new', 'approved_at' => now()]);

        return back()->with('status', 'Lead approved — it now shows in your Leads list.');
    }

    public function reject(Lead $lead): RedirectResponse
    {
        $lead->update(['status' => Lead::REJECTED]);

        return redirect()->route('leads.index')->with('status', 'Lead rejected.');
    }

    public function convert(Lead $lead): RedirectResponse
    {
        if ($lead->converted_customer_id) {
            return redirect()->route('customers.show', $lead->converted_customer_id);
        }

        $customer = Customer::create([
            'name' => $lead->name,
            'phone' => $lead->phone,
            'email' => $lead->email,
            'notes' => $lead->notes,
            'source' => 'lead',
        ]);

        $lead->update([
            'status' => 'booked',
            'converted_customer_id' => $customer->id,
        ]);

        return redirect()->route('customers.show', $customer)
            ->with('status', 'Lead converted to a customer — add their project/unit below.');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')->with('status', 'Lead removed.');
    }
}
