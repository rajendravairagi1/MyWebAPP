<?php

namespace App\Support;

/**
 * Best-effort video compression via ffmpeg, which most cPanel hosts do NOT
 * ship by default and which we have no SSH access to install ourselves.
 * available() is checked before every use; when it's false the caller
 * simply keeps the original upload — there is no queue worker on shared
 * hosting to fall back to, so this always runs inline with the upload
 * request rather than in the background.
 */
class VideoCompressor
{
    protected static ?bool $available = null;

    public static function available(): bool
    {
        if (self::$available !== null) {
            return self::$available;
        }

        if (! function_exists('exec') || ! function_exists('shell_exec')) {
            return self::$available = false;
        }

        $output = @shell_exec('ffmpeg -version 2>&1');

        return self::$available = ($output !== null && str_contains($output, 'ffmpeg version'));
    }

    public static function compress(string $sourcePath, string $destPath): bool
    {
        if (! self::available()) {
            return false;
        }

        $previousLimit = ini_get('max_execution_time');
        @set_time_limit(280);

        $source = escapeshellarg($sourcePath);
        $dest = escapeshellarg($destPath);

        $command = "ffmpeg -y -i {$source} -vf \"scale='min(1280,iw)':-2\" ".
            "-c:v libx264 -crf 28 -preset veryfast -c:a aac -b:a 96k {$dest} 2>&1";

        exec($command, $output, $exitCode);

        @set_time_limit((int) $previousLimit);

        return $exitCode === 0 && file_exists($destPath) && filesize($destPath) > 0;
    }
}
