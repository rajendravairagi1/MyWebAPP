<?php
/**
 * Tiny dependency-free SVG chart renderers (no JS charting library, no
 * Composer package - just PHP generating plain SVG markup server-side).
 * Kept intentionally simple: a gradient-filled area/line chart, a bar
 * chart, and a semi-circle gauge, matching the "dark dashboard" look.
 */

function svg_area_chart(array $labels, array $values, int $width = 600, int $height = 200, string $color = '#5eead4', string $id = ''): string
{
    $id = $id ?: 'ac' . substr(md5(implode(',', $values) . microtime()), 0, 8);
    $n = count($values);
    if ($n === 0) return '<p class="text-muted">No data yet.</p>';
    $max = max(max($values), 1);
    $padL = 6; $padR = 6; $padT = 14; $padB = 22;
    $plotW = $width - $padL - $padR;
    $plotH = $height - $padT - $padB;

    $points = [];
    foreach ($values as $i => $v) {
        $x = $padL + ($n > 1 ? ($i / ($n - 1)) * $plotW : $plotW / 2);
        $y = $padT + $plotH - ($v / $max) * $plotH;
        $points[] = [round($x, 2), round($y, 2)];
    }
    $polyline = implode(' ', array_map(fn($p) => $p[0] . ',' . $p[1], $points));
    $baseline = $padT + $plotH;
    $areaPath = 'M ' . $points[0][0] . ',' . $baseline . ' L ' . $polyline . ' L ' . end($points)[0] . ',' . $baseline . ' Z';

    $labelSvg = '';
    foreach (array_unique([0, intdiv($n, 2), $n - 1]) as $i) {
        if (!isset($labels[$i])) continue;
        $labelSvg .= '<text x="' . $points[$i][0] . '" y="' . ($height - 4) . '" font-size="10" fill="currentColor" opacity="0.55" text-anchor="middle">' . htmlspecialchars($labels[$i]) . '</text>';
    }

    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" height="' . $height . '" preserveAspectRatio="none" class="chart-svg">
      <defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="' . $color . '" stop-opacity="0.5"/>
        <stop offset="100%" stop-color="' . $color . '" stop-opacity="0"/>
      </linearGradient></defs>
      <path d="' . $areaPath . '" fill="url(#' . $id . ')" stroke="none"/>
      <polyline points="' . $polyline . '" fill="none" stroke="' . $color . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      ' . $labelSvg . '
    </svg>';
}

function svg_bar_chart(array $labels, array $values, int $width = 320, int $height = 200, string $color = '#5eead4'): string
{
    $n = count($values);
    if ($n === 0) return '<p class="text-muted">No data yet.</p>';
    $max = max(max($values), 1);
    $padT = 10; $padB = 24;
    $plotH = $height - $padT - $padB;
    $gap = 10;
    $barW = ($width - $gap * ($n + 1)) / $n;

    $svg = '';
    foreach ($values as $i => $v) {
        $h = ($v / $max) * $plotH;
        $x = $gap + $i * ($barW + $gap);
        $y = $padT + $plotH - $h;
        $opacity = round(0.45 + 0.55 * ($v / $max), 2);
        $svg .= '<rect x="' . round($x, 2) . '" y="' . round($y, 2) . '" width="' . round($barW, 2) . '" height="' . round(max($h, 2), 2) . '" rx="6" fill="' . $color . '" opacity="' . $opacity . '"/>';
        $lbl = isset($labels[$i]) ? mb_substr((string)$labels[$i], 0, 8) : '';
        $svg .= '<text x="' . round($x + $barW / 2, 2) . '" y="' . ($height - 6) . '" font-size="9.5" fill="currentColor" opacity="0.55" text-anchor="middle">' . htmlspecialchars($lbl) . '</text>';
    }
    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" height="' . $height . '" preserveAspectRatio="none" class="chart-svg">' . $svg . '</svg>';
}

function svg_gauge(float $percent, int $size = 200, string $color = '#5eead4'): string
{
    $percent = max(0, min(100, $percent));
    $r = $size / 2 - 14;
    $cx = $size / 2;
    $cy = $size / 2 + 4;
    $startX = round($cx - $r, 2);
    $endX = round($cx + $r, 2);
    $bgPath = "M $startX,$cy A $r,$r 0 0 1 $endX,$cy";

    $angle = 180 * ($percent / 100);
    $rad = deg2rad(180 - $angle);
    $fx = round($cx + $r * cos($rad), 2);
    $fy = round($cy - $r * sin($rad), 2);
    $fgPath = "M $startX,$cy A $r,$r 0 0,1 $fx,$fy";

    $viewH = (int)($size / 2 + 22);
    return '<svg viewBox="0 0 ' . $size . ' ' . $viewH . '" width="100%" height="' . $viewH . '">
      <path d="' . $bgPath . '" fill="none" stroke="currentColor" stroke-opacity="0.12" stroke-width="16" stroke-linecap="round"/>
      <path d="' . $fgPath . '" fill="none" stroke="' . $color . '" stroke-width="16" stroke-linecap="round"/>
    </svg>';
}
