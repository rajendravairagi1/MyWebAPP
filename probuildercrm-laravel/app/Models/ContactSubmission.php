<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSubmission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'plan', 'message'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * The number shown on the "Demo Requests" sidebar badge — capped at
     * "40+" rather than growing forever, since the badge only needs to
     * tell you "a lot are waiting", not the exact count. Null means no
     * badge at all (nothing unread).
     */
    public static function unreadBadge(): ?string
    {
        $count = static::whereNull('read_at')->count();

        if ($count === 0) {
            return null;
        }

        return $count > 40 ? '40+' : (string) $count;
    }
}
