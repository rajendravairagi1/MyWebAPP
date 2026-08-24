<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use Illuminate\View\View;

class CompletedProjectsController extends Controller
{
    public function index(): View
    {
        $units = ProjectUnit::with(['project', 'customer'])
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get();

        return view('completed-projects.index', compact('units'));
    }
}
