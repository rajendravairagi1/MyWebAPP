<?php

namespace App\Support;

use Illuminate\Support\Facades\File;

/**
 * Rewrites specific keys in the .env file in place, used only by the
 * browser-based installer (App\Http\Controllers\InstallController) so a
 * host without SSH/Composer access can still configure the database.
 */
class EnvWriter
{
    public static function set(array $values, ?string $path = null): void
    {
        $path ??= base_path('.env');

        $contents = File::exists($path) ? File::get($path) : '';

        foreach ($values as $key => $value) {
            $value = self::quote((string) $value);
            $pattern = '/^'.preg_quote($key, '/').'=.*/m';

            if (preg_match($pattern, $contents)) {
                $contents = preg_replace($pattern, "{$key}={$value}", $contents);
            } else {
                $contents .= "\n{$key}={$value}";
            }
        }

        File::put($path, $contents);
    }

    protected static function quote(string $value): string
    {
        if ($value === '' || preg_match('/\s|#|"/', $value)) {
            return '"'.str_replace('"', '\\"', $value).'"';
        }

        return $value;
    }
}
