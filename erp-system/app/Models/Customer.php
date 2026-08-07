<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'gst_number', 'address', 'contact_person', 'mobile',
    'email', 'payment_terms', 'opening_balance', 'notes',
])]
class Customer extends Model
{
    public function documents(): HasMany
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class)->latest('invoice_date');
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }
}
