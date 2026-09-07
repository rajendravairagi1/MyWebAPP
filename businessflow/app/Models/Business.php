<?php

namespace App\Models;

use App\Support\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'plan',
        'subscription_expires_at',
        'renewal_alert_dismissed_at',
        'is_demo',
        'smart_alerts_enabled',
        'payment_reminders_enabled',
        'voice_notes_enabled',
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
        'smart_alerts_enabled' => 'boolean',
        'payment_reminders_enabled' => 'boolean',
        'voice_notes_enabled' => 'boolean',
        'subscription_expires_at' => 'date',
        'renewal_alert_dismissed_at' => 'datetime',
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

    /**
     * The expiry date that actually gates access — a business inside a
     * branch has no billing of its own, so it inherits its Company's
     * expiry instead of its own (unset) column.
     */
    public function effectiveExpiresAt(): ?Carbon
    {
        if ($this->branch_id) {
            return $this->branch?->company?->subscription_expires_at;
        }

        return $this->subscription_expires_at;
    }

    /**
     * The symbol shown before every amount in this business's own pages/
     * PDFs — resolved from its `currency` column, never hardcoded ₹.
     */
    public function currencySymbol(): string
    {
        return static::symbolFor($this->currency);
    }

    public static function symbolFor(?string $currency): string
    {
        return config('business.currency_symbols')[$currency] ?? '₹';
    }

    public function isSubscriptionExpired(): bool
    {
        $expires = $this->effectiveExpiresAt();

        // The expiry date is the last day access is valid through, not the
        // first day it's cut off — so "expires 31 Aug" still works all day
        // on the 31st and only locks out starting the 1st.
        return $expires !== null && $expires->copy()->endOfDay()->isPast();
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
     * The token used in this business's public, no-login lead-capture form
     * link (and the QR code that encodes it) — generated once on first use
     * and kept stable after that so a QR code already printed or shared
     * never breaks.
     */
    public function leadFormToken(): string
    {
        if (! $this->lead_token) {
            $this->lead_token = \Illuminate\Support\Str::random(32);
            $this->save();
        }

        return $this->lead_token;
    }

    /**
     * The readable slug used in this business's public lead-form link
     * (e.g. /sharma-builders/QRcode) — a random token works but isn't
     * something a builder can recognise or hand out with confidence, so
     * this defaults to a slugified business name (made unique against
     * every other business) the first time it's needed, and stays stable
     * after that. A builder can override it from Business Settings via
     * setLeadFormSlug().
     */
    public function leadFormSlug(): string
    {
        if (! $this->lead_slug) {
            $this->lead_slug = self::uniqueLeadSlug($this->name ?: 'builder', $this->id);
            $this->save();
        }

        return $this->lead_slug;
    }

    /**
     * Generates a unique lead_slug from an arbitrary label, appending
     * -2, -3, … until it no longer collides with another business (or
     * this same business's current slug, via $exceptId).
     */
    public static function uniqueLeadSlug(string $label, ?int $exceptId = null): string
    {
        $base = \Illuminate\Support\Str::slug($label);
        if ($base === '') {
            $base = 'builder';
        }

        $slug = $base;
        $suffix = 2;

        while (
            self::where('lead_slug', $slug)
                ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * The full public lead-form URL to hand out/show as a QR code. A solo
     * business (no branch/company) gets domain/{slug}/QRcode; a business
     * under a Company gets domain/{company-slug}/{business-slug}/QRcode,
     * so the link itself shows which company a branch belongs to — the
     * company segment is set once at the Company level (Company::
     * publicSlug()) and shared by every branch under it, while each
     * business still sets its own trailing segment individually.
     */
    public function publicLeadUrl(): string
    {
        $company = $this->branch?->company;

        if ($company) {
            return route('leads.public.show-company-slug', [
                'companySlug' => $company->publicSlug(),
                'businessSlug' => $this->leadFormSlug(),
            ]);
        }

        return route('leads.public.show-slug', $this->leadFormSlug());
    }

    /**
     * Combined counts/totals for this business, used by the Branch/Company
     * dashboards to show a builder's numbers without switching into it.
     * totalCollected()/totalOutstanding() pull from invoices/payments,
     * which are themselves tenant-scoped — so this runs under a
     * temporary Tenant switch rather than just bypassing this model's
     * own scope, to keep those nested queries scoped correctly too.
     */
    /**
     * 'profit' here uses the same cash-basis formula as the Ledger
     * (collected + manual income − costs − manual expense) — money
     * actually received, not booked sale value — so a Company/Branch
     * dashboard rolling these up never disagrees with what a builder
     * sees on their own Ledger.
     */
    public function statsSummary(): array
    {
        return Tenant::runAs($this->id, function () {
            $units = ProjectUnit::whereNull('archived_at')->get();
            $collected = (float) $units->sum(fn ($u) => $u->totalCollected());
            $manualIncome = (float) LedgerEntry::where('type', 'income')->sum('amount');
            $manualExpense = (float) LedgerEntry::where('type', 'expense')->sum('amount');
            $cost = (float) ProjectCost::sum('amount') + $manualExpense;

            return [
                'projects' => Project::count(),
                'customers' => Customer::count(),
                'value' => (float) $units->sum('price'),
                'collected' => $collected,
                'outstanding' => (float) $units->sum(fn ($u) => $u->totalOutstanding()),
                'cost' => $cost,
                'profit' => $collected + $manualIncome - $cost,
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
        if (! $this->logo_path || ! Storage::disk('local')->exists($this->logo_path)) {
            return null;
        }

        $contents = Storage::disk('local')->get($this->logo_path);
        $mime = Storage::disk('local')->mimeType($this->logo_path);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
