<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobStageLog;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $jobs = Job::with('customer')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest('received_date')
            ->paginate(20)
            ->withQueryString();

        return view('jobs.index', ['jobs' => $jobs, 'status' => $status]);
    }

    public function create()
    {
        return view('jobs.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'challan_number' => ['nullable', 'string', 'max:100'],
            'vehicle_number' => ['nullable', 'string', 'max:50'],
            'photo' => ['nullable', 'image', 'max:4096'],
            'received_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:received_date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('job-photos', 'public');
        }

        $job = Job::create($validated);

        foreach ($validated['items'] as $item) {
            $job->items()->create($item);
        }

        return redirect()->route('jobs.show', $job)->with('status', 'Job receive ho gaya, Job ID #'.$job->id);
    }

    public function show(Job $job)
    {
        $job->load(['customer', 'items.product', 'stageLogs', 'materialIssues.rawMaterial']);

        return view('jobs.show', [
            'job' => $job,
            'stages' => Job::STAGES,
            'currentStage' => $job->currentStage(),
            'rawMaterials' => RawMaterial::orderBy('name')->get(),
        ]);
    }

    public function advanceStage(Request $request, Job $job)
    {
        $stage = $job->currentStage();

        if (! $stage) {
            return back()->with('status', 'Production pehle se hi complete hai.');
        }

        $validated = $request->validate([
            'employee_count' => ['nullable', 'integer', 'min:1'],
            'remarks' => ['nullable', 'string'],
            'qc_decision' => ['required_if:stage_is_qc,1', 'nullable', 'in:approved,rejected'],
        ]);

        if ($stage === 'Quality Check' && ($validated['qc_decision'] ?? null) === 'rejected') {
            // Rework loop: wipe logs from Paint onward so the job re-enters production there.
            $paintIndex = array_search('Paint', Job::STAGES);
            $reworkStages = array_slice(Job::STAGES, $paintIndex);

            $job->stageLogs()->whereIn('stage', $reworkStages)->delete();

            $job->update([
                'qc_status' => 'rejected',
                'qc_remarks' => $validated['remarks'] ?? 'QC reject — rework se Paint stage se dobara.',
            ]);

            return back()->with('status', 'QC reject hua — job wapas Paint stage me bhej diya gaya.');
        }

        JobStageLog::create([
            'job_order_id' => $job->id,
            'stage' => $stage,
            'started_at' => now(),
            'completed_at' => now(),
            'employee_count' => $validated['employee_count'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
        ]);

        if ($stage === 'Quality Check') {
            $job->update(['qc_status' => 'approved', 'qc_remarks' => $validated['remarks'] ?? null]);
        }

        if ($job->status === 'pending') {
            $job->update(['status' => 'working']);
        }

        if ($job->fresh()->isProductionComplete() && $job->qc_status === 'approved') {
            $job->update(['status' => 'ready']);
        }

        return back()->with('status', "\"{$stage}\" complete ho gaya.");
    }

    public function issueMaterial(Request $request, Job $job)
    {
        $validated = $request->validate([
            'raw_material_id' => ['required', 'exists:raw_materials,id'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'type' => ['required', 'in:issue,return'],
            'remarks' => ['nullable', 'string'],
        ]);

        $material = RawMaterial::findOrFail($validated['raw_material_id']);

        if ($validated['type'] === 'issue' && $material->current_stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => "Sirf {$material->current_stock} {$material->unit} stock me hai."]);
        }

        $job->materialIssues()->create([
            'raw_material_id' => $material->id,
            'quantity' => $validated['quantity'],
            'type' => $validated['type'],
            'issued_at' => now(),
            'remarks' => $validated['remarks'] ?? null,
        ]);

        $material->increment(
            'current_stock',
            $validated['type'] === 'issue' ? -$validated['quantity'] : $validated['quantity']
        );

        if ($job->status === 'pending') {
            $job->update(['status' => 'working']);
        }

        return back()->with('status', 'Material entry ho gayi.');
    }
}
