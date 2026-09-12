<?php

namespace App\Support;

/**
 * The platform-wide counterpart to LeadQrPoster — a single shareable PNG
 * "poster" for the public /get-started signup QR: the ProBuilderCRM logo
 * up top, the QR itself in a rounded card, then the brand name, website,
 * support email, and a short instruction — built server-side with GD so
 * what gets shared/downloaded is one complete branded image (like a
 * PhonePe/BHIM QR card) rather than a bare QR code. Unlike LeadQrPoster,
 * there's no Business to read a logo/name/contact from — this is one
 * poster for the whole platform, so everything is static branding.
 */
class SignupQrPoster
{
    private const WIDTH = 720;

    private const QR_SIZE = 460;

    /**
     * Returns null if GD (or FreeType text support within it) isn't
     * available on this server, or the shared brand logo can't be
     * fetched — the caller falls back to a bare QR code either way.
     */
    public static function build(string $publicUrl): ?string
    {
        if (! function_exists('imagettftext') || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        $fontDir = base_path('vendor/dompdf/dompdf/lib/fonts/');
        $fontBold = $fontDir.'DejaVuSans-Bold.ttf';
        $fontRegular = $fontDir.'DejaVuSans.ttf';
        $fontMono = $fontDir.'DejaVuSansMono.ttf';

        if (! is_file($fontBold) || ! is_file($fontRegular) || ! is_file($fontMono)) {
            return null;
        }

        try {
            $qrPng = DocumentQr::png($publicUrl, self::QR_SIZE);
            if (! $qrPng) {
                return null;
            }
            $qrImg = @imagecreatefromstring($qrPng);
            if (! $qrImg) {
                return null;
            }

            $logoImg = self::loadLogo();

            $width = self::WIDTH;
            $padX = 60;
            $contentWidth = $width - $padX * 2;

            $brand = config('app.name', 'ProBuilderCRM');
            $website = 'www.probuildercrm.com';
            $support = 'support@probuildercrm.com';
            $message = 'Fill the form and submit your request to start using ProBuilderCRM.';

            $messageLines = self::wrapText($fontBold, 17, $contentWidth, $message);

            // --- work out the canvas height by walking through the same
            // layout the draw pass below performs, without drawing yet ---
            $y = 50;
            $logoTargetH = 0;
            if ($logoImg) {
                $logoTargetH = self::scaledLogoHeight($logoImg);
                $y += $logoTargetH + 30;
            }
            $boxW = self::QR_SIZE + 48;
            $y += $boxW + 26; // QR card
            $y += self::lineHeight(22) + 6; // brand name
            $y += self::lineHeight(15) + 4; // website
            $y += self::lineHeight(15) + 22; // support email
            $y += count($messageLines) * self::lineHeight(17);
            $y += 50;

            $height = (int) $y;

            $im = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 0, 0, $white);

            $dark = imagecolorallocate($im, 24, 24, 27);
            $gray = imagecolorallocate($im, 100, 106, 118);
            $lightGray = imagecolorallocate($im, 244, 245, 247);
            $border = imagecolorallocate($im, 226, 229, 234);
            $brandColor = imagecolorallocate($im, 79, 70, 229);

            $cursorY = 50;

            if ($logoImg) {
                $lw = imagesx($logoImg);
                $lh = imagesy($logoImg);
                $targetH = $logoTargetH;
                $targetW = (int) round($lw * ($targetH / $lh));
                $dstX = (int) round(($width - $targetW) / 2);
                imagecopyresampled($im, $logoImg, $dstX, $cursorY, 0, 0, $targetW, $targetH, $lw, $lh);
                $cursorY += $targetH + 30;
            }

            $boxX1 = (int) round(($width - $boxW) / 2);
            $boxY1 = $cursorY;
            $boxX2 = $boxX1 + $boxW;
            $boxY2 = $boxY1 + $boxW;
            self::filledRoundedRect($im, $boxX1, $boxY1, $boxX2, $boxY2, 22, $lightGray);
            self::roundedRectOutline($im, $boxX1, $boxY1, $boxX2, $boxY2, 22, $border);
            imagecopyresampled($im, $qrImg, $boxX1 + 24, $boxY1 + 24, 0, 0, self::QR_SIZE, self::QR_SIZE, imagesx($qrImg), imagesy($qrImg));
            $cursorY = $boxY2 + 26;

            $cursorY = self::drawCenteredLine($im, $fontBold, 22, $brandColor, $width, $cursorY, $brand) + 6;
            $cursorY = self::drawCenteredLine($im, $fontRegular, 15, $gray, $width, $cursorY, $website) + 4;
            $cursorY = self::drawCenteredLine($im, $fontRegular, 15, $gray, $width, $cursorY, $support) + 22;

            foreach ($messageLines as $line) {
                $cursorY = self::drawCenteredLine($im, $fontBold, 17, $dark, $width, $cursorY, $line);
            }

            ob_start();
            imagepng($im);
            $png = ob_get_clean();

            imagedestroy($im);
            imagedestroy($qrImg);
            if ($logoImg) {
                imagedestroy($logoImg);
            }

            return $png ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Fetches the shared brand logo (same stable URL businessflow hard-
     * codes everywhere else, see BRAND_LOGO_URL) over plain HTTP rather
     * than reading a local file — unlike LeadQrPoster, there's no
     * per-business Business::logo_path to read from disk here.
     */
    private static function loadLogo(): mixed
    {
        $url = config('app.brand_logo_url');
        if (! $url) {
            return null;
        }

        try {
            $context = stream_context_create(['http' => ['timeout' => 5], 'https' => ['timeout' => 5]]);
            $binary = @file_get_contents($url, false, $context);
            if (! $binary) {
                return null;
            }

            $img = @imagecreatefromstring($binary);

            return $img ?: null;
        } catch (\Throwable) {
            return null;
        }
    }

    private static function scaledLogoHeight($logoImg): int
    {
        $lw = imagesx($logoImg);
        $lh = imagesy($logoImg);
        $targetH = 90;
        $targetW = (int) round($lw * ($targetH / $lh));
        if ($targetW > 320) {
            $targetH = (int) round($targetH * (320 / $targetW));
        }

        return $targetH;
    }

    private static function lineHeight(int $size): int
    {
        return (int) round($size * 1.4);
    }

    private static function drawCenteredLine($im, string $font, int $size, $color, int $canvasWidth, int $topY, string $text): int
    {
        $bbox = imagettfbbox($size, 0, $font, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $x = (int) round(($canvasWidth - $textWidth) / 2) - $bbox[0];
        $baselineY = $topY + (int) round($size * 0.92);
        imagettftext($im, $size, 0, $x, $baselineY, $color, $font, $text);

        return $topY + self::lineHeight($size);
    }

    /**
     * @return string[]
     */
    private static function wrapText(string $font, int $size, int $maxWidth, string $text): array
    {
        $words = preg_split('/\s+/', trim($text)) ?: [];
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            if (self::textWidth($font, $size, $word) > $maxWidth) {
                if ($current !== '') {
                    $lines[] = $current;
                    $current = '';
                }
                $chunk = '';
                foreach (mb_str_split($word) as $char) {
                    if (self::textWidth($font, $size, $chunk.$char) > $maxWidth && $chunk !== '') {
                        $lines[] = $chunk;
                        $chunk = $char;
                    } else {
                        $chunk .= $char;
                    }
                }
                $current = $chunk;

                continue;
            }

            $test = $current === '' ? $word : $current.' '.$word;
            if (self::textWidth($font, $size, $test) > $maxWidth && $current !== '') {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $test;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private static function textWidth(string $font, int $size, string $text): int
    {
        $bbox = imagettfbbox($size, 0, $font, $text);

        return $bbox[2] - $bbox[0];
    }

    private static function filledRoundedRect($im, int $x1, int $y1, int $x2, int $y2, int $r, $color): void
    {
        imagefilledrectangle($im, $x1 + $r, $y1, $x2 - $r, $y2, $color);
        imagefilledrectangle($im, $x1, $y1 + $r, $x2, $y2 - $r, $color);
        imagefilledellipse($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, $color);
        imagefilledellipse($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, $color);
        imagefilledellipse($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, $color);
        imagefilledellipse($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, $color);
    }

    private static function roundedRectOutline($im, int $x1, int $y1, int $x2, int $y2, int $r, $color): void
    {
        imagearc($im, $x1 + $r, $y1 + $r, $r * 2, $r * 2, 180, 270, $color);
        imagearc($im, $x2 - $r, $y1 + $r, $r * 2, $r * 2, 270, 360, $color);
        imagearc($im, $x1 + $r, $y2 - $r, $r * 2, $r * 2, 90, 180, $color);
        imagearc($im, $x2 - $r, $y2 - $r, $r * 2, $r * 2, 0, 90, $color);
        imageline($im, $x1 + $r, $y1, $x2 - $r, $y1, $color);
        imageline($im, $x1 + $r, $y2, $x2 - $r, $y2, $color);
        imageline($im, $x1, $y1 + $r, $x1, $y2 - $r, $color);
        imageline($im, $x2, $y1 + $r, $x2, $y2 - $r, $color);
    }
}
