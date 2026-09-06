<?php

namespace App\Http\Controllers;

use App\Models\ProjectUnit;
use App\Support\ListPagination;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompletedProjectsController extends Controller
{
    public function index(Request $request): View
    {
        $units = ProjectUnit::with(['project', 'customer'])
            ->whereNotNull('archived_at')
            ->when($request->string('q')->trim()->isNotEmpty(), fn ($q) => $q->where(function ($qq) use ($request) {
                $term = '%'.$request->string('q')->trim().'%';
                $qq->where('unit_number', 'like', $term)
                    ->orWhereHas('project', fn ($p) => $p->where('name', 'like', $term))
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', $term));
            }))
            ->orderByDesc('archived_at')
            ->paginate(ListPagination::perPage($request))
            ->withQueryString();

        return view('completed-projects.index', compact('units'));
    }
}
