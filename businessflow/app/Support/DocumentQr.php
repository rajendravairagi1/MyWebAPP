<?php

namespace App\Support;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * QR codes embedded in PDFs (Quotation, Invoice, Statement) so anyone
 * holding a printed/forwarded copy can scan and confirm it was actually
 * issued by this business. PNG (not SVG) because DomPDF's image support
 * for base64 PNG data-URIs is far more reliable than its inline-SVG
 * support, and GD (needed to render PNG) is near-universal on shared
 * PHP hosting.
 */
class DocumentQr
{
    /**
     * Returns null (instead of throwing) if QR rendering isn't possible
     * on this server — e.g. the GD extension is missing — so a PDF still
     * downloads without its verification QR rather than failing outright.
     */
    public static function dataUri(string $url, int $size = 130): ?string
    {
        try {
            $qrCode = new QrCode($url, size: $size, margin: 4);
            $writer = new PngWriter();

            return $writer->write($qrCode)->getDataUri();
        } catch (\Throwable) {
            return null;
        }
    }
}
