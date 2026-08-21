<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Followup;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Project;
use App\Models\Quotation;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResetDataController extends Controller
{
    public function index(Request $request): View
    {
        $this->checkToken((string) $request->query('token'));

        return view('reset-data', ['token' => $request->query('token')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->checkToken((string) $request->input('token'));

        $request->validate([
            'confirm' => ['required', 'in:RESET'],
        ]);

        abort_unless(Tenant::check(), 403, 'No active business.');

        DB::transaction(function () {
            CustomerDocument::each(function (CustomerDocument $document) {
                Storage::disk('local')->delete($document->path);
            });

            Invoice::query()->delete();
            Quotation::query()->delete();
            Followup::query()->delete();
            CustomerDocument::query()->delete();
            Customer::query()->delete();
            Project::query()->delete();
            Product::query()->delete();
        });

        return redirect()->route('dashboard')->with('status', 'All business data cleared. Your login stays the same — start adding fresh projects and customers.');
    }

    protected function checkToken(string $token): void
    {
        $expected = config('app.install_token');

        abort_unless(filled($expected) && hash_equals($expected, $token), 403, 'Missing or invalid token.');
    }
}
