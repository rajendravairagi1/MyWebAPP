<?php
/**
 * SimplePDF - a tiny, self-contained PDF writer with no external
 * dependencies (no Composer needed - works on any shared PHP hosting).
 * Supports the small subset of PDF needed for invoices/statements:
 * text at absolute coordinates, straight lines, and rectangles, using
 * the built-in Helvetica core font (regular + bold).
 *
 * Coordinate system: origin (0,0) at TOP-LEFT of the page, in points,
 * matching how you'd normally lay out a printed document. Internally it
 * is flipped to PDF's bottom-left origin.
 */
class SimplePDF
{
    private float $pageW = 595.28; // A4 width in points
    private float $pageH = 841.89; // A4 height in points
    private array $ops = [];
    private string $font = 'F1'; // F1 = Helvetica, F2 = Helvetica-Bold

    public function setFont(bool $bold = false): void
    {
        $this->font = $bold ? 'F2' : 'F1';
    }

    private function esc(string $s): string
    {
        $s = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $s);
        // Core Helvetica only supports WinAnsi; transliterate the rupee sign.
        $s = str_replace('₹', 'Rs.', $s);
        return $s;
    }

    public function text(float $x, float $y, string $text, int $size = 10, bool $bold = false, string $color = '#000000'): void
    {
        $this->setFont($bold);
        $py = $this->pageH - $y;
        [$r, $g, $b] = $this->hexToRgb($color);
        $this->ops[] = sprintf('%.3F %.3F %.3F rg BT /%s %d Tf %.2F %.2F Td (%s) Tj ET', $r, $g, $b, $this->font, $size, $x, $py, $this->esc($text));
    }

    /** Same as text(), but $xRight is the RIGHT edge to align the text against (for numbers/amounts). */
    public function textRight(float $xRight, float $y, string $text, int $size = 10, bool $bold = false, string $color = '#000000'): void
    {
        $this->text($xRight - $this->textWidth($text, $size), $y, $text, $size, $bold, $color);
    }

    /**
     * Approximate rendered width of $text at $size using standard Helvetica
     * AFM glyph widths (per 1000 units of em) for the characters we
     * actually use in tables (digits, currency punctuation, letters).
     */
    public function textWidth(string $text, int $size): float
    {
        static $widths = [
            '0'=>556,'1'=>556,'2'=>556,'3'=>556,'4'=>556,'5'=>556,'6'=>556,'7'=>556,'8'=>556,'9'=>556,
            ','=>278, '.'=>278, '-'=>333, ' '=>278, ':'=>278, '/'=>278,
            'R'=>722, 's'=>500,
        ];
        $w = 0;
        foreach (str_split($text) as $ch) {
            $w += ($widths[$ch] ?? 560) / 1000 * $size;
        }
        return $w;
    }

    private function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        return [hexdec(substr($hex, 0, 2)) / 255, hexdec(substr($hex, 2, 2)) / 255, hexdec(substr($hex, 4, 2)) / 255];
    }

    public function line(float $x1, float $y1, float $x2, float $y2, float $width = 0.5, string $color = '#000000'): void
    {
        [$r, $g, $b] = $this->hexToRgb($color);
        $y1 = $this->pageH - $y1;
        $y2 = $this->pageH - $y2;
        $this->ops[] = sprintf('%.3F %.3F %.3F RG %.2F w %.2F %.2F m %.2F %.2F l S', $r, $g, $b, $width, $x1, $y1, $x2, $y2);
    }

    public function rect(float $x, float $y, float $w, float $h, float $lineWidth = 0.5, string $color = '#000000'): void
    {
        [$r, $g, $b] = $this->hexToRgb($color);
        $py = $this->pageH - $y - $h;
        $this->ops[] = sprintf('%.3F %.3F %.3F RG %.2F w %.2F %.2F %.2F %.2F re S', $r, $g, $b, $lineWidth, $x, $py, $w, $h);
    }

    /** A solid filled rectangle - used for the dark summary/total boxes. */
    public function rectFilled(float $x, float $y, float $w, float $h, string $color): void
    {
        [$r, $g, $b] = $this->hexToRgb($color);
        $py = $this->pageH - $y - $h;
        $this->ops[] = sprintf('%.3F %.3F %.3F rg %.2F %.2F %.2F %.2F re f', $r, $g, $b, $x, $py, $w, $h);
    }

    public function pageWidth(): float { return $this->pageW; }
    public function pageHeight(): float { return $this->pageH; }

    /**
     * Wraps $text to fit within $maxWidth (approx, using average glyph
     * width for Helvetica) and prints each line, returning the Y after
     * the last line.
     */
    public function multiline(float $x, float $y, string $text, float $maxWidth, int $size = 10, float $lineHeight = 14): float
    {
        $words = preg_split('/\s+/', trim($text));
        $avgChar = $size * 0.52;
        $maxChars = max(1, (int)floor($maxWidth / $avgChar));
        $line = '';
        foreach ($words as $word) {
            $candidate = $line === '' ? $word : $line . ' ' . $word;
            if (strlen($candidate) > $maxChars) {
                $this->text($x, $y, $line, $size);
                $y += $lineHeight;
                $line = $word;
            } else {
                $line = $candidate;
            }
        }
        if ($line !== '') {
            $this->text($x, $y, $line, $size);
            $y += $lineHeight;
        }
        return $y;
    }

    public function output(string $filename, string $dest = 'I'): void
    {
        $content = implode("\n", $this->ops);
        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";
        $objects[2] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
        $objects[3] = sprintf(
            "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2F %.2F] /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> /Contents 4 0 R >>",
            $this->pageW,
            $this->pageH
        );
        $objects[4] = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[5] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
        $objects[6] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $num => $body) {
            $offsets[$num] = strlen($pdf);
            $pdf .= $num . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xrefStart = strlen($pdf);
        $count = count($objects) + 1;
        $pdf .= "xref\n0 {$count}\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= "trailer\n<< /Size {$count} /Root 1 0 R >>\nstartxref\n{$xrefStart}\n%%EOF";

        if ($dest === 'S') {
            // Return as string (caller handles output) - not used currently.
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: ' . ($dest === 'D' ? 'attachment' : 'inline') . '; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        echo $pdf;
    }
}
