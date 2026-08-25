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
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'permissions', 'status'])
            ->withTimestamps();
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
