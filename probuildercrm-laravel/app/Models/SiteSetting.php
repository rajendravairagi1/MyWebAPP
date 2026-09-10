<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    const THEMES = ['dark', 'light'];
    const DEFAULT_THEME = 'dark';
    const DEFAULT_ADMIN_THEME = 'dark';

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("site_setting.{$key}");
    }

    public static function theme(): string
    {
        return Cache::rememberForever('site_setting.theme', function () {
            return static::get('theme', self::DEFAULT_THEME);
        });
    }

    public static function setTheme(string $theme): void
    {
        if (! in_array($theme, self::THEMES, true)) {
            throw new \InvalidArgumentException("Unknown theme: {$theme}");
        }

        static::set('theme', $theme);
    }

    /**
     * Independent from theme()/setTheme() above - this one controls how
     * the admin panel itself looks for you, not what visitors see on the
     * public site.
     */
    public static function adminTheme(): string
    {
        return Cache::rememberForever('site_setting.admin_theme', function () {
            return static::get('admin_theme', self::DEFAULT_ADMIN_THEME);
        });
    }

    public static function setAdminTheme(string $theme): void
    {
        if (! in_array($theme, self::THEMES, true)) {
            throw new \InvalidArgumentException("Unknown theme: {$theme}");
        }

        static::set('admin_theme', $theme);
    }
}
