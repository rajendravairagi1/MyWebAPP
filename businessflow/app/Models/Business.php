<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
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
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
    }
}
