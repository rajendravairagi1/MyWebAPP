<?php

namespace App\Models\Concerns;

use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;

/**
 * Every tenant-owned model (Customer, Lead, Quotation, Invoice, ...) uses
 * this trait instead of filtering business_id by hand in each query.
 * The active tenant always comes from App\Support\Tenant, which is only
 * ever populated by the IdentifyTenant middleware from the session.
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Tenant::check()) {
                $builder->where($builder->getModel()->getTable().'.business_id', Tenant::id());
            }
        });

        static::creating(function ($model) {
            if (! $model->business_id && Tenant::check()) {
                $model->business_id = Tenant::id();
            }
        });
    }

    public function business()
    {
        return $this->belongsTo(\App\Models\Business::class);
    }
}
