<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Custom pivot model for business_user, needed so ->pivot->permissions
 * comes back as a decoded array. BelongsToMany's withCasts() (a
 * Builder-level method it merely proxies through) does NOT cast pivot
 * columns in this Laravel version — only a pivot model's own $casts
 * does, which is why the relations use ->using(self::class) rather
 * than withCasts(). With that in place, attach()/updateExistingPivot()
 * also encode a plain PHP array through this cast on write — passing
 * an already-json_encode()'d string here would double-encode it.
 */
class BusinessUser extends Pivot
{
    protected $casts = [
        'permissions' => 'array',
    ];
}
