<?php
/**
 * QRCode - a tiny, self-contained QR code encoder (no external library,
 * no Composer package - works on any shared PHP hosting). Supports byte
 * mode only (fine for URLs), versions 1-6, error correction level M.
 * That's enough for a ~100 character verification URL.
 *
 * Implements ISO/IEC 18004: Reed-Solomon error correction over GF(256),
 * standard matrix placement (finder/timing/alignment patterns, format
 * info), and best-of-8 data masking.
 */
class QRCode
{
    // Per-version tables (index 0 = version 1) for error correction level M.
    private const TOTAL_CODEWORDS = [26, 44, 70, 100, 134, 172];
    private const EC_PER_BLOCK = [10, 16, 26, 18, 24, 16];
    private const NUM_BLOCKS = [1, 1, 1, 2, 2, 4];
    private const REMAINDER_BITS = [0, 7, 7, 7, 7, 7];
    private const ALIGNMENT_POS = [[], [6, 18], [6, 22], [6, 26], [6, 30], [6, 34]];

    private static array $expTable = [];
    private static array $logTable = [];

    private static function initTables(): void
    {
        if (self::$expTable) return;
        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$expTable[$i] = $x;
            self::$logTable[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) $x ^= 0x11D;
        }
        for ($i = 255; $i < 512; $i++) self::$expTable[$i] = self::$expTable[$i - 255];
    }

    private static function gfMul(int $a, int $b): int
    {
        if ($a === 0 || $b === 0) return 0;
        return self::$expTable[self::$logTable[$a] + self::$logTable[$b]];
    }

    private static function generatorPoly(int $degree): array
    {
        $result = array_fill(0, $degree, 0);
        $result[$degree - 1] = 1;
        $root = 1;
        for ($i = 0; $i < $degree; $i++) {
            for ($j = 0; $j < $degree; $j++) {
                $result[$j] = self::gfMul($result[$j], $root);
                if ($j + 1 < $degree) $result[$j] ^= $result[$j + 1];
            }
            $root = self::gfMul($root, 2);
        }
        return $result;
    }

    private static function computeEcc(array $data, int $eccLen): array
    {
        $generator = self::generatorPoly($eccLen);
        $result = array_fill(0, $eccLen, 0);
        foreach ($data as $b) {
            $factor = $b ^ $result[0];
            array_shift($result);
            $result[] = 0;
            for ($i = 0; $i < $eccLen; $i++) {
                $result[$i] ^= self::gfMul($generator[$i], $factor);
            }
        }
        return $result;
    }

    /** Returns a square boolean matrix (true = dark module) encoding $text. */
    public static function encodeMatrix(string $text): array
    {
        self::initTables();
        $bytes = array_values(unpack('C*', $text));
        $version = null;
        foreach (self::TOTAL_CODEWORDS as $i => $total) {
            $dataCw = $total - self::EC_PER_BLOCK[$i] * self::NUM_BLOCKS[$i];
            $capacityBits = $dataCw * 8;
            $neededBits = 4 + 8 + count($bytes) * 8; // mode(4) + count(8, v1-9) + data
            if ($neededBits <= $capacityBits) { $version = $i + 1; break; }
        }
        if ($version === null) {
            throw new RuntimeException('Text too long for supported QR versions (max ~100 bytes).');
        }
        $idx = $version - 1;
        $dataCw = self::TOTAL_CODEWORDS[$idx] - self::EC_PER_BLOCK[$idx] * self::NUM_BLOCKS[$idx];

        // ---- Build the data bitstream ----
        $bits = '0100' . str_pad(decbin(count($bytes)), 8, '0', STR_PAD_LEFT);
        foreach ($bytes as $b) $bits .= str_pad(decbin($b), 8, '0', STR_PAD_LEFT);
        $capacityBits = $dataCw * 8;
        $bits .= str_repeat('0', min(4, $capacityBits - strlen($bits)));
        while (strlen($bits) % 8 !== 0) $bits .= '0';
        $padBytes = ['11101100', '00010001'];
        $p = 0;
        while (strlen($bits) < $capacityBits) { $bits .= $padBytes[$p % 2]; $p++; }
        $bits = substr($bits, 0, $capacityBits);

        $dataWords = [];
        for ($i = 0; $i < $capacityBits; $i += 8) $dataWords[] = bindec(substr($bits, $i, 8));

        // ---- Split into blocks, compute EC, interleave ----
        $numBlocks = self::NUM_BLOCKS[$idx];
        $perBlock = intdiv($dataCw, $numBlocks);
        $eccLen = self::EC_PER_BLOCK[$idx];
        $blocks = array_chunk($dataWords, $perBlock);
        $eccBlocks = array_map(fn($blk) => self::computeEcc($blk, $eccLen), $blocks);

        $final = [];
        for ($i = 0; $i < $perBlock; $i++) {
            foreach ($blocks as $blk) $final[] = $blk[$i];
        }
        for ($i = 0; $i < $eccLen; $i++) {
            foreach ($eccBlocks as $blk) $final[] = $blk[$i];
        }
        $finalBits = '';
        foreach ($final as $w) $finalBits .= str_pad(decbin($w), 8, '0', STR_PAD_LEFT);
        $finalBits .= str_repeat('0', self::REMAINDER_BITS[$idx]);

        // ---- Build the matrix ----
        $size = 17 + 4 * $version;
        $matrix = array_fill(0, $size, array_fill(0, $size, false));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));

        $setFn = function (&$m, &$fn, $x, $y, $val) use ($size) {
            if ($x < 0 || $x >= $size || $y < 0 || $y >= $size) return;
            $m[$y][$x] = $val;
            $fn[$y][$x] = true;
        };
        $drawFinder = function (&$m, &$fn, $cx, $cy) use ($setFn) {
            for ($dy = -4; $dy <= 4; $dy++) {
                for ($dx = -4; $dx <= 4; $dx++) {
                    $d = max(abs($dx), abs($dy));
                    $val = $d !== 2 && $d <= 3;
                    if ($d <= 4) $setFn($m, $fn, $cx + $dx, $cy + $dy, $d <= 3 ? $val : false);
                }
            }
        };
        $drawFinder($matrix, $isFunction, 3, 3);
        $drawFinder($matrix, $isFunction, $size - 4, 3);
        $drawFinder($matrix, $isFunction, 3, $size - 4);

        // Timing patterns
        for ($i = 8; $i < $size - 8; $i++) {
            $setFn($matrix, $isFunction, $i, 6, $i % 2 === 0);
            $setFn($matrix, $isFunction, 6, $i, $i % 2 === 0);
        }

        // Alignment patterns - versions 2-6 each have exactly one, centered at
        // the last coordinate in ALIGNMENT_POS (the other combinations all
        // overlap one of the 3 finder patterns and are skipped by spec).
        $pos = self::ALIGNMENT_POS[$idx];
        if (count($pos) >= 2) {
            $ax = $ay = end($pos);
            for ($dy = -2; $dy <= 2; $dy++) {
                for ($dx = -2; $dx <= 2; $dx++) {
                    $d = max(abs($dx), abs($dy));
                    $setFn($matrix, $isFunction, $ax + $dx, $ay + $dy, $d !== 1);
                }
            }
        }

        // Reserve format info areas (filled in after mask selection) + dark module
        for ($i = 0; $i < 9; $i++) {
            $setFn($matrix, $isFunction, $i, 8, false);
            $setFn($matrix, $isFunction, 8, $i, false);
        }
        for ($i = 0; $i < 8; $i++) {
            $setFn($matrix, $isFunction, $size - 1 - $i, 8, false);
            $setFn($matrix, $isFunction, 8, $size - 1 - $i, false);
        }
        $setFn($matrix, $isFunction, 8, $size - 8, true); // dark module

        // ---- Place data bits (zig-zag, right to left, skipping column 6) ----
        $bitIdx = 0;
        $bitLen = strlen($finalBits);
        $upward = true;
        for ($col = $size - 1; $col >= 1; $col -= 2) {
            if ($col === 6) $col--;
            for ($n = 0; $n < $size; $n++) {
                $row = $upward ? $size - 1 - $n : $n;
                foreach ([$col, $col - 1] as $x) {
                    if ($isFunction[$row][$x]) continue;
                    $bit = $bitIdx < $bitLen ? $finalBits[$bitIdx] === '1' : false;
                    $matrix[$row][$x] = $bit;
                    $bitIdx++;
                }
            }
            $upward = !$upward;
        }

        // ---- Try all 8 masks, keep the one with the lowest penalty ----
        $bestMask = 0;
        $bestPenalty = PHP_INT_MAX;
        $bestMatrix = null;
        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = self::applyMask($matrix, $isFunction, $mask);
            self::writeFormatInfo($candidate, $isFunction, $mask, $size);
            $penalty = self::penaltyScore($candidate, $size);
            if ($penalty < $bestPenalty) { $bestPenalty = $penalty; $bestMask = $mask; $bestMatrix = $candidate; }
        }

        return $bestMatrix;
    }

    private static function applyMask(array $matrix, array $isFunction, int $mask): array
    {
        $size = count($matrix);
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($isFunction[$y][$x]) continue;
                $invert = match ($mask) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($y, 2) + intdiv($x, 3)) % 2 === 0,
                    5 => ($x * $y) % 2 + ($x * $y) % 3 === 0,
                    6 => (($x * $y) % 2 + ($x * $y) % 3) % 2 === 0,
                    7 => (($x + $y) % 2 + ($x * $y) % 3) % 2 === 0,
                };
                if ($invert) $matrix[$y][$x] = !$matrix[$y][$x];
            }
        }
        return $matrix;
    }

    private static function writeFormatInfo(array &$matrix, array $isFunction, int $mask, int $size): void
    {
        $eccBits = 0b00; // level M
        $data = ($eccBits << 3) | $mask;
        $rem = $data;
        for ($i = 0; $i < 10; $i++) $rem = ($rem << 1) ^ ((($rem >> 9) & 1) * 0x537);
        $rem &= 0x3FF;
        $bits = (($data << 10) | $rem) ^ 0x5412;
        // The position table below numbers bit0 at the first-placed module,
        // but the codeword's bit0 is its LSB (last placed per spec) - so the
        // bit order must be reversed before placement.
        $reversed = 0;
        for ($i = 0; $i < 15; $i++) $reversed |= (($bits >> $i) & 1) << (14 - $i);
        $bits = $reversed;

        for ($i = 0; $i <= 5; $i++) $matrix[8][$i] = (($bits >> $i) & 1) === 1;
        $matrix[8][7] = (($bits >> 6) & 1) === 1;
        $matrix[8][8] = (($bits >> 7) & 1) === 1;
        $matrix[7][8] = (($bits >> 8) & 1) === 1;
        for ($i = 9; $i <= 14; $i++) $matrix[14 - $i][8] = (($bits >> $i) & 1) === 1;

        for ($i = 0; $i <= 6; $i++) $matrix[$size - 1 - $i][8] = (($bits >> $i) & 1) === 1;
        for ($i = 7; $i <= 14; $i++) $matrix[8][$size - 15 + $i] = (($bits >> $i) & 1) === 1;
    }

    private static function penaltyScore(array $m, int $size): int
    {
        $penalty = 0;
        for ($y = 0; $y < $size; $y++) {
            $run = 1;
            for ($x = 1; $x < $size; $x++) {
                if ($m[$y][$x] === $m[$y][$x - 1]) { $run++; } else { if ($run >= 5) $penalty += 3 + ($run - 5); $run = 1; }
            }
            if ($run >= 5) $penalty += 3 + ($run - 5);
        }
        for ($x = 0; $x < $size; $x++) {
            $run = 1;
            for ($y = 1; $y < $size; $y++) {
                if ($m[$y][$x] === $m[$y - 1][$x]) { $run++; } else { if ($run >= 5) $penalty += 3 + ($run - 5); $run = 1; }
            }
            if ($run >= 5) $penalty += 3 + ($run - 5);
        }
        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $v = $m[$y][$x];
                if ($v === $m[$y][$x + 1] && $v === $m[$y + 1][$x] && $v === $m[$y + 1][$x + 1]) $penalty += 3;
            }
        }
        $dark = 0;
        foreach ($m as $row) $dark += count(array_filter($row));
        $percent = ($dark * 100) / ($size * $size);
        $penalty += (int)(abs((int)$percent - 50) / 5) * 10;
        return $penalty;
    }

    /** Renders the matrix as a self-contained SVG string. */
    public static function svg(string $text, int $moduleSize = 4, int $quietZone = 4): string
    {
        $m = self::encodeMatrix($text);
        $size = count($m);
        $px = ($size + $quietZone * 2) * $moduleSize;
        $rects = '';
        foreach ($m as $y => $row) {
            foreach ($row as $x => $dark) {
                if ($dark) {
                    $rects .= '<rect x="' . (($x + $quietZone) * $moduleSize) . '" y="' . (($y + $quietZone) * $moduleSize) . '" width="' . $moduleSize . '" height="' . $moduleSize . '"/>';
                }
            }
        }
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $px . ' ' . $px . '" width="' . $px . '" height="' . $px . '" shape-rendering="crispEdges"><rect width="100%" height="100%" fill="#fff"/><g fill="#000">' . $rects . '</g></svg>';
    }
}
