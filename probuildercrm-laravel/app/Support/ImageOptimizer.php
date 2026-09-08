<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

/**
 * Resizes and re-compresses an uploaded image before it lands on disk, so
 * every image the site serves — blog images, the logo — stays web-sized
 * instead of whatever multi-megabyte file a phone camera produced. Uses
 * plain GD (a PHP extension, not a Composer package) since vendor/ on
 * shared hosting is never refreshed by a zip-extract deploy.
 */
class ImageOptimizer
{
    public static function optimizeAndSave(UploadedFile $file, string $destinationPath, int $maxWidth = 1600, int $quality = 78): void
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $directory = dirname($destinationPath);
        $filename = basename($destinationPath);

        if ($extension === 'svg' || ! extension_loaded('gd')) {
            $file->move($directory, $filename);

            return;
        }

        $source = match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'png' => @imagecreatefrompng($file->getRealPath()),
            'webp' => @imagecreatefromwebp($file->getRealPath()),
            default => null,
        };

        if (! $source) {
            $file->move($directory, $filename);

            return;
        }

        $width = imagesx($source);
        $height = imagesy($source);

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if ($extension === 'png' || $extension === 'webp') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($source);
            $source = $resized;
        }

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($source, $destinationPath, $quality),
            'png' => imagepng($source, $destinationPath, 6),
            'webp' => imagewebp($source, $destinationPath, $quality),
        };

        imagedestroy($source);
    }
}
