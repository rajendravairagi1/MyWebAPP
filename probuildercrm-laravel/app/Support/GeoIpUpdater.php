<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * Downloads MaxMind's free GeoLite2-Country database — the "Update GeoIP
 * Database" button in Admin > Pricing calls this, so keeping it current
 * never needs SSH or a cron job on hosts that don't offer one. Requires
 * a free MaxMind account + license key (GEOIP_LICENSE_KEY in .env) —
 * see the instructions on the admin page for how to get one.
 */
class GeoIpUpdater
{
    private const DOWNLOAD_URL = 'https://download.maxmind.com/app/geoip_download';

    /**
     * @throws \RuntimeException on any failure — the message is safe to
     *         show directly in the admin UI (no license key/secrets in it).
     */
    public static function update(): void
    {
        $licenseKey = config('services.maxmind.license_key');

        if (! $licenseKey) {
            throw new \RuntimeException('No MaxMind license key configured — add MAXMIND_LICENSE_KEY to the .env file first.');
        }

        $response = Http::timeout(30)->get(self::DOWNLOAD_URL, [
            'edition_id' => 'GeoLite2-Country',
            'license_key' => $licenseKey,
            'suffix' => 'tar.gz',
        ]);

        if (! $response->ok()) {
            throw new \RuntimeException('MaxMind download failed (HTTP '.$response->status().') — double-check the license key is correct and active.');
        }

        $tmpTarball = tempnam(sys_get_temp_dir(), 'geolite2').'.tar.gz';
        file_put_contents($tmpTarball, $response->body());

        $tmpExtractDir = sys_get_temp_dir().'/geolite2-'.uniqid();
        File::makeDirectory($tmpExtractDir, 0755, true);

        try {
            $phar = new \PharData($tmpTarball);
            $phar->extractTo($tmpExtractDir);

            // MaxMind ships it inside a dated folder like
            // GeoLite2-Country_20260101/GeoLite2-Country.mmdb — the exact
            // date changes every release, so find it rather than hardcode it.
            $mmdbFiles = File::allFiles($tmpExtractDir);
            $mmdb = collect($mmdbFiles)->first(fn ($f) => $f->getExtension() === 'mmdb');

            if (! $mmdb) {
                throw new \RuntimeException('Downloaded file didn\'t contain a .mmdb database — MaxMind may have changed their file format.');
            }

            File::ensureDirectoryExists(dirname(Geo::databasePath()));
            File::copy($mmdb->getPathname(), Geo::databasePath());
        } finally {
            @unlink($tmpTarball);
            File::deleteDirectory($tmpExtractDir);
        }
    }
}
