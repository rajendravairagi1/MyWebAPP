<?php

namespace App\Http\Controllers;

use App\Models\MaterialEntry;
use App\Models\ProjectUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MaterialEntryController extends Controller
{
    public function store(Request $request, ProjectUnit $unit): RedirectResponse
    {
        $data = $request->validate([
            'material_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_label' => ['nullable', 'string', 'max:50'],
            'direction' => ['required', 'in:in,out'],
            'entered_on' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $unit->materialEntries()->create($data);

        return back()->with('status', 'Material entry added.');
    }

    public function update(Request $request, ProjectUnit $unit, MaterialEntry $entry): RedirectResponse
    {
        abort_unless($entry->project_unit_id === $unit->id, 404);

        $data = $request->validate([
            'material_name' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'numeric', 'min:0.01'],
            'unit_label' => ['nullable', 'string', 'max:50'],
            'direction' => ['required', 'in:in,out'],
            'entered_on' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $entry->update($data);

        return back()->with('status', 'Material entry updated.');
    }

    public function destroy(ProjectUnit $unit, MaterialEntry $entry): RedirectResponse
    {
        abort_unless($entry->project_unit_id === $unit->id, 404);

        $entry->delete();

        return back()->with('status', 'Material entry removed.');
    }
}
