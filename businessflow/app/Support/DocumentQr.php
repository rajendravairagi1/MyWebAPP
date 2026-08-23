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
    public static function dataUri(string $url, int $size = 130): string
    {
        $qrCode = new QrCode($url, size: $size, margin: 4);
        $writer = new PngWriter();

        return $writer->write($qrCode)->getDataUri();
    }
}
