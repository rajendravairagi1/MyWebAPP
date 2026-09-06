<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * The per-page choice offered by <x-list-toolbar> (10/20/50) — shared here
 * so every index() controller validates it the same way instead of each
 * repeating its own allow-list and default.
 */
class ListPagination
{
    public static function perPage(Request $request, int $default = 20): int
    {
        $value = (int) $request->get('per_page', $default);

        return in_array($value, [10, 20, 50], true) ? $value : $default;
    }
}
