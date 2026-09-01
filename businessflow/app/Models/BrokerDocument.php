<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrokerDocument extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'business_id',
        'broker_id',
        'name',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function broker(): BelongsTo
    {
        return $this->belongsTo(Broker::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function humanSize(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }
}
