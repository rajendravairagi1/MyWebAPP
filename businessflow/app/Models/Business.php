<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'plan',
        'is_demo',
        'name',
        'address',
        'phone',
        'email',
        'website',
        'business_type',
        'country',
        'currency',
        'timezone',
        'tax_config',
        'invoice_prefix',
        'logo_path',
        'enabled_modules',
    ];

    protected $casts = [
        'tax_config' => 'array',
        'enabled_modules' => 'array',
        'is_demo' => 'boolean',
    ];

    /**
     * This business's effective plan tier for App\Support\Tenant::planAllows().
     * A business inside a branch is always at least 'company' tier — that's
     * what being part of a Company/Branch means — regardless of its own
     * 'plan' column (which only matters for a standalone business).
     */
    public function effectivePlan(): string
    {
        return $this->branch_id ? 'company' : $this->plan;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(BusinessUser::class)
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Combined counts/totals for this business, used by the Branch/Company
     * dashboards to show a builder's numbers without switching into it.
     * totalCollected()/totalOutstanding() pull from invoices/payments,
     * which are themselves tenant-scoped — so this runs under a
     * temporary Tenant switch rather than just bypassing this model's
     * own scope, to keep those nested queries scoped correctly too.
     */
    public function statsSummary(): array
    {
        return \App\Support\Tenant::runAs($this->id, function () {
            $units = \App\Models\ProjectUnit::whereNull('archived_at')->get();

            return [
                'projects' => \App\Models\Project::count(),
                'customers' => \App\Models\Customer::count(),
                'value' => (float) $units->sum('price'),
                'collected' => (float) $units->sum(fn ($u) => $u->totalCollected()),
                'outstanding' => (float) $units->sum(fn ($u) => $u->totalOutstanding()),
            ];
        });
    }

    /**
     * The logo as a base64 data URI, for embedding directly in PDFs.
     * DomPDF's image loader doesn't carry the app's session/auth, so a
     * normal authenticated route to the file won't render there — the
     * data URI sidesteps that entirely, same as the verify QR codes.
     */
    public function logoDataUri(): ?string
    {
        if (! $this->logo_path || ! \Illuminate\Support\Facades\Storage::disk('local')->exists($this->logo_path)) {
            return null;
        }

        $contents = \Illuminate\Support\Facades\Storage::disk('local')->get($this->logo_path);
        $mime = \Illuminate\Support\Facades\Storage::disk('local')->mimeType($this->logo_path);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
