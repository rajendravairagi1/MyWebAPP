<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quotation;
use App\Support\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Global header search — grouped results across Customers, Projects,
     * Quotations, and Invoices. Each group is skipped when the user
     * lacks that module's permission, same as the sidebar links, so a
     * team member never sees results for a module they can't open.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        if (Tenant::can('customers')) {
            $customers = Customer::query()
                ->where(fn ($query) => $query->where('name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('company', 'like', "%{$q}%"))
                ->limit(5)
                ->get();

            if ($customers->isNotEmpty()) {
                $results[] = [
                    'group' => __('Customers'),
                    'items' => $customers->map(fn ($c) => [
                        'title' => $c->name,
                        'subtitle' => $c->phone ?: $c->email,
                        'url' => route('customers.show', $c),
                        'badge' => null,
                    ]),
                ];
            }
        }

        if (Tenant::can('projects')) {
            $projects = Project::query()
                ->where('name', 'like', "%{$q}%")
                ->limit(5)
                ->get();

            if ($projects->isNotEmpty()) {
                $results[] = [
                    'group' => __('Projects'),
                    'items' => $projects->map(fn ($p) => [
                        'title' => $p->name,
                        'subtitle' => $p->location,
                        'url' => route('projects.show', $p),
                        'badge' => null,
                    ]),
                ];
            }
        }

        if (Tenant::can('quotations')) {
            $quotations = Quotation::with('customer')
                ->where(fn ($query) => $query->where('number', 'like', "%{$q}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")))
                ->limit(5)
                ->get();

            if ($quotations->isNotEmpty()) {
                $results[] = [
                    'group' => __('Quotations'),
                    'items' => $quotations->map(fn ($quotation) => [
                        'title' => $quotation->number,
                        'subtitle' => $quotation->customer?->name,
                        'url' => route('quotations.show', $quotation),
                        'badge' => ucfirst($quotation->status),
                    ]),
                ];
            }
        }

        if (Tenant::can('invoices')) {
            $invoices = Invoice::with('customer')
                ->where(fn ($query) => $query->where('number', 'like', "%{$q}%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$q}%")))
                ->limit(5)
                ->get();

            if ($invoices->isNotEmpty()) {
                $results[] = [
                    'group' => __('Invoices'),
                    'items' => $invoices->map(fn ($invoice) => [
                        'title' => $invoice->number,
                        'subtitle' => $invoice->customer?->name,
                        'url' => route('invoices.show', $invoice),
                        'badge' => ucfirst($invoice->status),
                    ]),
                ];
            }
        }

        return response()->json(['results' => $results]);
    }
}
