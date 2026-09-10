<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = ['owner_user_id', 'name', 'phone', 'subscription_expires_at', 'renewal_alert_dismissed_at', 'status'];

    protected $casts = [
        'subscription_expires_at' => 'date',
        'renewal_alert_dismissed_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function isActive(): bool
    {
        return $this->status !== 'inactive';
    }

    /**
     * The readable slug used as the first segment of every business under
     * this company's public lead-form link (domain/{slug}/{business-slug}
     * /QRcode) — defaults to a slugified, uniqueness-checked company name,
     * generated the first time it's needed and stable after that. Shared
     * by every branch/business under this company, unlike a business's own
     * lead_slug which each business sets individually.
     */
    public function publicSlug(): string
    {
        if (! $this->slug) {
            $this->slug = self::uniqueSlug($this->name ?: 'company', $this->id);
            $this->save();
        }

        return $this->slug;
    }

    public static function uniqueSlug(string $label, ?int $exceptId = null): string
    {
        $base = \Illuminate\Support\Str::slug($label);
        if ($base === '') {
            $base = 'company';
        }

        $slug = $base;
        $suffix = 2;

        while (
            self::where('slug', $slug)
                ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
