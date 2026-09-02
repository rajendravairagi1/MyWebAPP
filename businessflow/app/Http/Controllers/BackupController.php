<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerDocument;
use App\Models\Followup;
use App\Models\Investor;
use App\Models\InvestorTransaction;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LedgerEntry;
use App\Models\MaterialEntry;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\ProjectCost;
use App\Models\ProjectUnit;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\UnitMedia;
use App\Models\UnitPayment;
use App\Support\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use ZipArchive;

/**
 * A full data backup/restore for the currently active business —
 * available to anyone with owner-level access to it (a business's own
 * Owner, or a Company Owner/Branch Manager who has switched into one of
 * their builders, since Tenant::isOwner() covers all three). Reachable
 * only via whatever business is currently active, matching how every
 * other page in this app scopes itself — a Company Owner backs up
 * "everyone" simply by visiting this page once per builder they can
 * already switch into.
 *
 * The backup is a single .zip containing a structured data.json (every
 * tenant-scoped row, so restore can recreate them with fresh IDs and
 * remapped foreign keys rather than replaying raw SQL against a shared
 * multi-tenant database where the original primary keys are almost
 * certainly already taken by someone else's rows) plus a media/ folder
 * with the actual uploaded files (customer photos, Aadhar, documents,
 * unit media, cost bills) at their original storage paths.
 */
class BackupController extends Controller
{
    /**
     * Tables in an order safe to INSERT during restore (every foreign
     * key a table points at — within this same backup set — already
     * exists by the time that table's turn comes up).
     */
    private const RESTORE_ORDER = [
        'products', 'investors', 'customers', 'projects',
        'project_units', 'project_costs', 'customer_documents', 'followups', 'investor_transactions',
        'quotations', 'unit_payments', 'material_entries', 'unit_media',
        'quotation_items', 'invoices',
        'invoice_items', 'payments',
        'ledger_entries',
    ];

    public function index(): View
    {
        $business = \App\Models\Business::findOrFail(Tenant::id());

        return view('backup.index', compact('business'));
    }

    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $business = \App\Models\Business::findOrFail(Tenant::id());

        $tables = $this->exportTables();
        $mediaPaths = $this->collectMediaPaths($tables);

        $payload = [
            'app' => 'BusinessFlow',
            'backup_version' => 1,
            'exported_at' => now()->toIso8601String(),
            'business_id' => $business->id,
            'business_name' => $business->name,
            'tables' => $tables,
        ];

        $zipPath = storage_path('app/tmp-backup-'.Str::uuid().'.zip');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE);
        $zip->addFromString('data.json', json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        foreach ($mediaPaths as $relativePath) {
            if (Storage::disk('local')->exists($relativePath)) {
                $zip->addFile(Storage::disk('local')->path($relativePath), 'media/'.$relativePath);
            }
        }

        $zip->close();

        $filename = Str::slug($business->name).'-backup-'.now()->format('Y-m-d').'.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }

    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'backup' => ['required', 'file', 'mimes:zip', 'max:102400'],
            'confirm' => ['required', 'in:RESTORE'],
        ]);

        $business = \App\Models\Business::findOrFail(Tenant::id());

        $zip = new ZipArchive();
        $tmpUpload = $request->file('backup')->getRealPath();

        if ($zip->open($tmpUpload) !== true) {
            return back()->withErrors(['backup' => 'Could not read that file — make sure it\'s the .zip a BusinessFlow backup produced.']);
        }

        $json = $zip->getFromName('data.json');

        if (! $json) {
            $zip->close();

            return back()->withErrors(['backup' => 'This doesn\'t look like a BusinessFlow backup — data.json is missing from the zip.']);
        }

        $payload = json_decode($json, true);

        if (! is_array($payload) || ($payload['app'] ?? null) !== 'BusinessFlow' || ! isset($payload['tables'])) {
            $zip->close();

            return back()->withErrors(['backup' => 'This doesn\'t look like a valid BusinessFlow backup file.']);
        }

        try {
            DB::transaction(function () use ($payload) {
                $this->wipeCurrentBusinessData();
                $this->restoreTables($payload['tables']);
            });
        } catch (\Throwable $e) {
            $zip->close();
            report($e);

            return back()->withErrors(['backup' => 'Restore failed and nothing was changed — '.$e->getMessage()]);
        }

        $this->restoreMediaFiles($zip);
        $zip->close();

        return redirect()->route('dashboard')->with('status', "\"{$business->name}\" restored from backup — every project, customer, quotation, invoice and payment is back.");
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function exportTables(): array
    {
        $customers = Customer::withTrashed()->get();
        $projects = Project::all();
        $projectUnits = ProjectUnit::all();
        $quotations = Quotation::all();
        $invoices = Invoice::all();

        return [
            'products' => Product::all()->toArray(),
            'investors' => Investor::all()->toArray(),
            'customers' => $customers->toArray(),
            'projects' => $projects->toArray(),
            'project_units' => $projectUnits->toArray(),
            'project_costs' => ProjectCost::all()->toArray(),
            'customer_documents' => CustomerDocument::all()->toArray(),
            'followups' => Followup::all()->toArray(),
            'investor_transactions' => InvestorTransaction::all()->toArray(),
            'quotations' => $quotations->toArray(),
            'unit_payments' => UnitPayment::all()->toArray(),
            'material_entries' => MaterialEntry::all()->toArray(),
            'unit_media' => UnitMedia::all()->toArray(),
            'quotation_items' => QuotationItem::whereIn('quotation_id', $quotations->pluck('id'))->get()->toArray(),
            'invoices' => $invoices->toArray(),
            'invoice_items' => InvoiceItem::whereIn('invoice_id', $invoices->pluck('id'))->get()->toArray(),
            'payments' => Payment::whereIn('invoice_id', $invoices->pluck('id'))->get()->toArray(),
            'ledger_entries' => LedgerEntry::all()->toArray(),
        ];
    }

    /**
     * @param  array<string, array<int, array<string, mixed>>>  $tables
     * @return array<int, string>
     */
    private function collectMediaPaths(array $tables): array
    {
        $paths = [];

        foreach ($tables['customers'] as $row) {
            if ($row['photo_path'] ?? null) {
                $paths[] = $row['photo_path'];
            }
            if ($row['aadhar_path'] ?? null) {
                $paths[] = $row['aadhar_path'];
            }
        }

        foreach ($tables['customer_documents'] as $row) {
            if ($row['path'] ?? null) {
                $paths[] = $row['path'];
            }
        }

        foreach ($tables['unit_media'] as $row) {
            if ($row['path'] ?? null) {
                $paths[] = $row['path'];
            }
        }

        foreach ($tables['project_costs'] as $row) {
            if ($row['bill_path'] ?? null) {
                $paths[] = $row['bill_path'];
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * Same table list/order as export, deleted children-first so every
     * foreign key inside this business's own data is gone before its
     * parent — cross-business rows are untouched since every model here
     * is tenant-scoped to the currently active business.
     */
    private function wipeCurrentBusinessData(): void
    {
        foreach (array_reverse(self::RESTORE_ORDER) as $table) {
            match ($table) {
                'products' => Product::query()->delete(),
                'investors' => Investor::query()->delete(),
                'customers' => Customer::withTrashed()->forceDelete(),
                'projects' => Project::query()->delete(),
                'project_units' => ProjectUnit::query()->delete(),
                'project_costs' => ProjectCost::query()->delete(),
                'customer_documents' => CustomerDocument::query()->delete(),
                'followups' => Followup::query()->delete(),
                'investor_transactions' => InvestorTransaction::query()->delete(),
                'quotations' => Quotation::query()->delete(),
                'unit_payments' => UnitPayment::query()->delete(),
                'material_entries' => MaterialEntry::query()->delete(),
                'unit_media' => UnitMedia::query()->delete(),
                // Quotation/Invoice rows themselves are deleted later in this
                // same reversed pass, so they're still scoped and present now.
                'quotation_items' => QuotationItem::whereIn('quotation_id', Quotation::pluck('id'))->delete(),
                'invoices' => Invoice::query()->delete(),
                'invoice_items' => InvoiceItem::whereIn('invoice_id', Invoice::pluck('id'))->delete(),
                'payments' => Payment::query()->delete(),
                'ledger_entries' => LedgerEntry::query()->delete(),
                default => null,
            };
        }
    }

    /**
     * Re-creates every row with a fresh auto-increment id (the original
     * ids almost certainly collide with other businesses' rows in this
     * shared database) and rewrites each foreign key to the new id its
     * referenced row ended up with. Columns that reference a user
     * (created_by, recorded_by, owner_id, uploaded_by) are left as-is
     * when that user still exists, else nulled — restoring is always
     * into an account that's still logged in, so these are almost
     * always still valid.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $tables
     */
    private function restoreTables(array $tables): void
    {
        $ids = []; // [table => [old_id => new_id]]
        $validUserIds = \App\Models\User::pluck('id')->flip();

        $remapUser = function (?int $userId) use ($validUserIds) {
            return $userId && $validUserIds->has($userId) ? $userId : null;
        };
        // A plain closure with an explicit by-reference `use` is required
        // here — arrow functions (fn) capture $ids by value at the point
        // they're defined, so they'd never see rows inserted afterward.
        $remap = function (string $table, $oldId) use (&$ids) {
            return $oldId === null ? null : ($ids[$table][$oldId] ?? null);
        };

        foreach ($tables['products'] as $row) {
            $ids['products'][$row['id']] = Product::forceCreate($this->withTimestamps($row, [
                'name', 'sku', 'type', 'unit', 'price', 'tax_rate', 'stock_qty', 'low_stock_threshold',
            ]))->id;
        }

        foreach ($tables['investors'] as $row) {
            $ids['investors'][$row['id']] = Investor::forceCreate($this->withTimestamps($row, [
                'name', 'phone', 'email', 'notes',
            ]))->id;
        }

        foreach ($tables['customers'] as $row) {
            $attrs = $this->withTimestamps($row, [
                'name', 'company', 'phone', 'email', 'address', 'notes', 'source', 'tags', 'photo_path', 'aadhar_path', 'aadhar_name',
            ]);
            $attrs['deleted_at'] = $row['deleted_at'] ?? null;
            $ids['customers'][$row['id']] = Customer::forceCreate($attrs)->id;
        }

        foreach ($tables['projects'] as $row) {
            $ids['projects'][$row['id']] = Project::forceCreate($this->withTimestamps($row, [
                'name', 'type', 'location', 'status', 'start_date', 'expected_completion_date', 'notes',
            ]))->id;
        }

        foreach ($tables['project_units'] as $row) {
            $attrs = $this->withTimestamps($row, [
                'unit_number', 'type', 'area_sqft', 'price', 'status', 'commitment_date', 'commitment_note',
                'archived_at', 'write_off_amount', 'write_off_note', 'write_off_at',
            ]);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $ids['project_units'][$row['id']] = ProjectUnit::forceCreate($attrs)->id;
        }

        foreach ($tables['project_costs'] as $row) {
            $attrs = $this->withTimestamps($row, [
                'category', 'description', 'amount', 'spent_on', 'vendor', 'notes', 'bill_path', 'bill_name',
            ]);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $ids['project_costs'][$row['id']] = ProjectCost::forceCreate($attrs)->id;
        }

        foreach ($tables['customer_documents'] as $row) {
            $attrs = $this->withTimestamps($row, ['name', 'path', 'mime_type', 'size']);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['uploaded_by'] = $remapUser($row['uploaded_by'] ?? null);
            $ids['customer_documents'][$row['id']] = CustomerDocument::forceCreate($attrs)->id;
        }

        foreach ($tables['followups'] as $row) {
            $attrs = $this->withTimestamps($row, ['note', 'category', 'due_at', 'status']);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['owner_id'] = $remapUser($row['owner_id'] ?? null);
            $ids['followups'][$row['id']] = Followup::forceCreate($attrs)->id;
        }

        foreach ($tables['investor_transactions'] as $row) {
            $attrs = $this->withTimestamps($row, ['type', 'amount', 'transaction_date', 'method', 'reference', 'description']);
            $attrs['investor_id'] = $remap('investors', $row['investor_id']);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['recorded_by'] = $remapUser($row['recorded_by'] ?? null);
            $ids['investor_transactions'][$row['id']] = InvestorTransaction::forceCreate($attrs)->id;
        }

        foreach ($tables['quotations'] as $row) {
            $attrs = $this->withTimestamps($row, [
                'number', 'status', 'valid_until', 'subtotal', 'discount_total', 'tax_total', 'total', 'notes', 'terms',
            ]);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['project_unit_id'] = $remap('project_units', $row['project_unit_id']);
            $attrs['created_by'] = $remapUser($row['created_by'] ?? null);
            $ids['quotations'][$row['id']] = Quotation::forceCreate($attrs)->id;
        }

        foreach ($tables['unit_payments'] as $row) {
            $attrs = $this->withTimestamps($row, ['amount', 'purpose', 'description', 'method', 'paid_at', 'reference', 'notes']);
            $attrs['project_unit_id'] = $remap('project_units', $row['project_unit_id']);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['recorded_by'] = $remapUser($row['recorded_by'] ?? null);
            $ids['unit_payments'][$row['id']] = UnitPayment::forceCreate($attrs)->id;
        }

        foreach ($tables['material_entries'] as $row) {
            $attrs = $this->withTimestamps($row, ['material_name', 'quantity', 'unit_label', 'direction', 'entered_on', 'note']);
            $attrs['project_unit_id'] = $remap('project_units', $row['project_unit_id']);
            $ids['material_entries'][$row['id']] = MaterialEntry::forceCreate($attrs)->id;
        }

        foreach ($tables['unit_media'] as $row) {
            $attrs = $this->withTimestamps($row, ['type', 'path', 'original_name', 'mime_type', 'size']);
            $attrs['project_unit_id'] = $remap('project_units', $row['project_unit_id']);
            $attrs['uploaded_by'] = $remapUser($row['uploaded_by'] ?? null);
            $ids['unit_media'][$row['id']] = UnitMedia::forceCreate($attrs)->id;
        }

        foreach ($tables['quotation_items'] as $row) {
            $attrs = $this->withTimestamps($row, ['description', 'quantity', 'unit_price', 'discount', 'tax_rate', 'line_total']);
            $attrs['quotation_id'] = $remap('quotations', $row['quotation_id']);
            $attrs['product_id'] = $remap('products', $row['product_id']);
            QuotationItem::forceCreate($attrs);
        }

        foreach ($tables['invoices'] as $row) {
            $attrs = $this->withTimestamps($row, [
                'number', 'status', 'due_date', 'subtotal', 'discount_total', 'tax_total', 'total', 'amount_paid',
                'notes', 'counts_toward_property_price',
            ]);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['project_unit_id'] = $remap('project_units', $row['project_unit_id']);
            $attrs['quotation_id'] = $remap('quotations', $row['quotation_id']);
            $attrs['unit_payment_id'] = $remap('unit_payments', $row['unit_payment_id']);
            $attrs['created_by'] = $remapUser($row['created_by'] ?? null);
            $ids['invoices'][$row['id']] = Invoice::forceCreate($attrs)->id;
        }

        foreach ($tables['invoice_items'] as $row) {
            $attrs = $this->withTimestamps($row, ['description', 'quantity', 'unit_price', 'discount', 'tax_rate', 'line_total']);
            $attrs['invoice_id'] = $remap('invoices', $row['invoice_id']);
            $attrs['product_id'] = $remap('products', $row['product_id']);
            InvoiceItem::forceCreate($attrs);
        }

        foreach ($tables['payments'] as $row) {
            $attrs = $this->withTimestamps($row, ['amount', 'method', 'paid_at', 'reference', 'notes']);
            $attrs['invoice_id'] = $remap('invoices', $row['invoice_id']);
            $attrs['recorded_by'] = $remapUser($row['recorded_by'] ?? null);
            Payment::forceCreate($attrs);
        }

        foreach ($tables['ledger_entries'] as $row) {
            $attrs = $this->withTimestamps($row, ['type', 'category', 'description', 'amount', 'entry_date']);
            $attrs['customer_id'] = $remap('customers', $row['customer_id']);
            $attrs['project_id'] = $remap('projects', $row['project_id']);
            $attrs['recorded_by'] = $remapUser($row['recorded_by'] ?? null);
            LedgerEntry::forceCreate($attrs);
        }
    }

    /**
     * Picks the given columns out of a backed-up row and carries its
     * original created_at/updated_at through — business_id is
     * deliberately never copied from the backup: forceCreate() still
     * runs the BelongsToTenant creating() hook, which stamps the
     * currently active business, so a restore always lands in whichever
     * business you're viewing it from.
     */
    private function withTimestamps(array $row, array $columns): array
    {
        $attrs = array_intersect_key($row, array_flip($columns));
        $attrs['created_at'] = $row['created_at'] ?? now();
        $attrs['updated_at'] = $row['updated_at'] ?? now();

        return $attrs;
    }

    private function restoreMediaFiles(ZipArchive $zip): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (! str_starts_with($name, 'media/')) {
                continue;
            }

            $relativePath = substr($name, strlen('media/'));
            $contents = $zip->getFromIndex($i);

            if ($contents !== false && $relativePath !== '') {
                Storage::disk('local')->put($relativePath, $contents);
            }
        }
    }
}
