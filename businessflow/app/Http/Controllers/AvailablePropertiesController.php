<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use Illuminate\View\View;

class AvailablePropertiesController extends Controller
{
    public function index(): View
    {
        $units = ProjectUnit::with(['project', 'photos', 'videos'])
            ->where('status', 'available')
            ->whereNull('archived_at')
            ->get()
            ->sortBy([['project.name', 'asc'], ['unit_number', 'asc']]);

        return view('available-properties.index', compact('units'));
    }
}
