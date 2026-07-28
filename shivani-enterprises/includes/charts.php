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
    $niceMax = _nice_axis_max($max);
    $ticks = 4;
    $padL = 56; $padR = 12; $padT = 18; $padB = 28;
    $plotW = $width - $padL - $padR;
    $plotH = $height - $padT - $padB;

    $points = [];
    foreach ($values as $i => $v) {
        $x = $padL + ($n > 1 ? ($i / ($n - 1)) * $plotW : $plotW / 2);
        $y = $padT + $plotH - ($v / $niceMax) * $plotH;
        $points[] = [round($x, 2), round($y, 2)];
    }
    $polyline = implode(' ', array_map(fn($p) => $p[0] . ',' . $p[1], $points));
    $baseline = $padT + $plotH;
    $areaPath = 'M ' . $points[0][0] . ',' . $baseline . ' L ' . $polyline . ' L ' . end($points)[0] . ',' . $baseline . ' Z';

    // Gridlines + Y-axis money labels.
    $grid = '';
    for ($t = 0; $t <= $ticks; $t++) {
        $yv = $niceMax * ($t / $ticks);
        $y = $padT + $plotH - ($yv / $niceMax) * $plotH;
        $grid .= '<line x1="' . $padL . '" y1="' . round($y, 2) . '" x2="' . ($padL + $plotW) . '" y2="' . round($y, 2) . '" stroke="currentColor" stroke-opacity="0.1" stroke-width="1"/>';
        $grid .= '<text x="' . ($padL - 8) . '" y="' . round($y + 4, 2) . '" class="tick" font-size="11" text-anchor="end">' . _short_money($yv) . '</text>';
    }

    // Dots on each data point + X-axis date labels at a few positions.
    $dots = '';
    foreach ($points as $i => $p) {
        if ($values[$i] > 0) $dots .= '<circle cx="' . $p[0] . '" cy="' . $p[1] . '" r="3" style="fill: ' . $color . '"/>';
    }
    $labelSvg = '';
    foreach (array_unique([0, intdiv($n, 2), $n - 1]) as $i) {
        if (!isset($labels[$i])) continue;
        $labelSvg .= '<text class="axis" x="' . $points[$i][0] . '" y="' . ($height - 8) . '" font-size="11.5" text-anchor="middle">' . htmlspecialchars($labels[$i]) . '</text>';
    }

    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" class="chart-svg" style="height:auto;max-height:' . $height . 'px">
      <defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" style="stop-color: ' . $color . '" stop-opacity="0.55"/>
        <stop offset="100%" style="stop-color: ' . $color . '" stop-opacity="0"/>
      </linearGradient></defs>
      ' . $grid . '
      <path d="' . $areaPath . '" fill="url(#' . $id . ')" stroke="none"/>
      <polyline points="' . $polyline . '" fill="none" style="stroke: ' . $color . '" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      ' . $dots . '
      ' . $labelSvg . '
    </svg>';
}

function svg_bar_chart(array $labels, array $values, int $width = 700, int $height = 320, string $color = '#5eead4'): string
{
    $n = count($values);
    if ($n === 0) return '<p class="text-muted">No data yet.</p>';
    $max = max(max($values), 1);
    // Round the axis top up to a nice number (e.g. 42,300 -> 50,000) so
    // the gridlines land on readable values instead of arbitrary fractions.
    $niceMax = _nice_axis_max($max);
    $ticks = 4;

    $padL = 62; $padR = 18; $padT = 22; $padB = 60;
    $plotW = $width - $padL - $padR;
    $plotH = $height - $padT - $padB;
    $gap = 14;
    $barW = ($plotW - $gap * ($n - 1)) / $n;
    // Cap single/few bars so we don't get a giant stretched rectangle when
    // there are only 1-2 products in the list. Gridlines still span the
    // full plot width; only the bars get centered.
    $maxBarW = 80;
    $barsWidth = $n * $barW + ($n - 1) * $gap;
    $barsPadL = $padL;
    if ($barW > $maxBarW) {
        $barW = $maxBarW;
        $barsWidth = $n * $barW + ($n - 1) * $gap;
        $barsPadL = $padL + ($plotW - $barsWidth) / 2;
    }

    $id = 'bg' . substr(md5(implode(',', $values) . microtime()), 0, 8);

    $svg = '<defs><linearGradient id="' . $id . '" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" style="stop-color: ' . $color . '" stop-opacity="1"/>
        <stop offset="100%" style="stop-color: ' . $color . '" stop-opacity="0.55"/>
      </linearGradient></defs>';

    // Horizontal gridlines + Y-axis tick labels (₹ short form).
    for ($t = 0; $t <= $ticks; $t++) {
        $yv = $niceMax * ($t / $ticks);
        $y = $padT + $plotH - ($yv / $niceMax) * $plotH;
        $svg .= '<line x1="' . $padL . '" y1="' . round($y, 2) . '" x2="' . ($padL + $plotW) . '" y2="' . round($y, 2) . '" stroke="currentColor" stroke-opacity="0.12" stroke-width="1"/>';
        $svg .= '<text x="' . ($padL - 8) . '" y="' . round($y + 4, 2) . '" class="tick" font-size="11" text-anchor="end">' . _short_money($yv) . '</text>';
    }
    // Baseline (X axis) — a slightly bolder line for grounding.
    $svg .= '<line x1="' . $padL . '" y1="' . ($padT + $plotH) . '" x2="' . ($padL + $plotW) . '" y2="' . ($padT + $plotH) . '" stroke="currentColor" stroke-opacity="0.35" stroke-width="1"/>';

    foreach ($values as $i => $v) {
        $h = ($v / $niceMax) * $plotH;
        $h = max($h, 2);
        $x = $barsPadL + $i * ($barW + $gap);
        $y = $padT + $plotH - $h;
        $svg .= '<rect x="' . round($x, 2) . '" y="' . round($y, 2) . '" width="' . round($barW, 2) . '" height="' . round($h, 2) . '" rx="6" fill="url(#' . $id . ')"/>';

        // Value label sitting on top of each bar.
        $svg .= '<text class="bar-value" x="' . round($x + $barW / 2, 2) . '" y="' . round($y - 6, 2) . '" font-size="12" text-anchor="middle">' . _short_money((float)$v) . '</text>';

        // X-axis label under the bar, rotated 30° so long product names fit.
        $lbl = (string)($labels[$i] ?? '');
        if (mb_strlen($lbl) > 14) $lbl = mb_substr($lbl, 0, 13) . '…';
        $lx = round($x + $barW / 2, 2);
        $ly = $padT + $plotH + 14;
        $svg .= '<text class="axis" x="' . $lx . '" y="' . $ly . '" font-size="11.5" text-anchor="end" transform="rotate(-30 ' . $lx . ' ' . $ly . ')">' . htmlspecialchars($lbl) . '</text>';
    }
    return '<svg viewBox="0 0 ' . $width . ' ' . $height . '" width="100%" class="chart-svg" style="height:auto;max-height:' . $height . 'px">' . $svg . '</svg>';
}

// Round a positive value up to a "nice" axis top like 100, 250, 500, 1k, 2.5k…
// so bar-chart gridlines land on readable numbers.
function _nice_axis_max(float $v): float
{
    if ($v <= 0) return 1.0;
    $exp = floor(log10($v));
    $pow = pow(10, $exp);
    $frac = $v / $pow;
    if     ($frac <= 1)   $nice = 1;
    elseif ($frac <= 2)   $nice = 2;
    elseif ($frac <= 2.5) $nice = 2.5;
    elseif ($frac <= 5)   $nice = 5;
    else                  $nice = 10;
    return $nice * $pow;
}

// Compact rupee formatting for chart labels: 132000 -> "₹1.3L", 8500 -> "₹8.5k".
function _short_money(float $v): string
{
    if ($v >= 10000000) return '₹' . rtrim(rtrim(number_format($v / 10000000, 1), '0'), '.') . 'Cr';
    if ($v >= 100000)   return '₹' . rtrim(rtrim(number_format($v / 100000, 1), '0'), '.') . 'L';
    if ($v >= 1000)     return '₹' . rtrim(rtrim(number_format($v / 1000, 1), '0'), '.') . 'k';
    return '₹' . number_format($v, 0);
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
      <path d="' . $fgPath . '" fill="none" style="stroke: ' . $color . '" stroke-width="16" stroke-linecap="round"/>
    </svg>';
}
