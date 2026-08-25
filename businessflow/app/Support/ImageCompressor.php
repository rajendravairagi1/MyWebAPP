<?php

namespace App\Support;

/**
 * Resizes/re-encodes an uploaded photo using PHP's built-in GD extension,
 * which every mainstream cPanel PHP build ships with — unlike ffmpeg, this
 * never needs a check for availability. Used as both the primary photo
 * compressor and a safety net behind any client-side compression, since a
 * browser can be bypassed (old browser, API upload, etc.) but the server
 * step can't.
 */
class ImageCompressor
{
    public static function compress(string $sourcePath, string $destPath, int $maxWidth = 1600, int $quality = 75): bool
    {
        if (! extension_loaded('gd')) {
            return false;
        }

        $info = @getimagesize($sourcePath);

        if (! $info) {
            return false;
        }

        [$width, $height, $type] = $info;

        $image = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default => false,
        };

        if (! $image) {
            return false;
        }

        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);

            if (! empty($exif['Orientation']) && $exif['Orientation'] !== 1) {
                $image = self::rotateByExif($image, (int) $exif['Orientation']);
                $width = imagesx($image);
                $height = imagesy($image);
            }
        }

        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($height * ($maxWidth / $width));
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if ($type === IMAGETYPE_PNG) {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        $result = match ($type) {
            IMAGETYPE_JPEG => imagejpeg($image, $destPath, $quality),
            IMAGETYPE_PNG => imagepng($image, $destPath, 6),
            IMAGETYPE_WEBP => imagewebp($image, $destPath, $quality),
            default => false,
        };

        imagedestroy($image);

        return (bool) $result;
    }

    protected static function rotateByExif(\GdImage $image, int $orientation): \GdImage
    {
        $rotated = match ($orientation) {
            3 => imagerotate($image, 180, 0),
            6 => imagerotate($image, -90, 0),
            8 => imagerotate($image, 90, 0),
            default => $image,
        };

        return $rotated ?: $image;
    }
}
