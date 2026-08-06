<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
{
    public function index()
    {
        $materials = RawMaterial::orderBy('name')->get();

        return view('raw-materials.index', ['materials' => $materials]);
    }

    public function create()
    {
        return view('raw-materials.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        $material = RawMaterial::create($validated);

        return redirect()->route('raw-materials.index')->with('status', "{$material->name} add ho gaya.");
    }

    public function edit(RawMaterial $rawMaterial)
    {
        return view('raw-materials.edit', ['material' => $rawMaterial]);
    }

    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $validated = $this->validated($request);

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')->with('status', 'Material update ho gaya.');
    }

    public function destroy(RawMaterial $rawMaterial)
    {
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')->with('status', "{$rawMaterial->name} delete ho gaya.");
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'string', 'max:50'],
            'current_stock' => ['required', 'numeric', 'min:0'],
            'low_stock_alert_at' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
