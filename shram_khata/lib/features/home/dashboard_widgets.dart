import 'dart:math' as math;

import 'package:flutter/material.dart';

import '../../core/dates.dart';
import '../../core/money.dart';
import '../../core/theme.dart';
import '../../data/services/dashboard_service.dart';

/// Chart colours, validated for colour-blind separation on a white surface.
class ChartColors {
  ChartColors._();
  static const billing = Color(0xFF0D9488);
  static const cost = Color(0xFFD97706);
}

/// Donut showing how today's headcount splits into present / half / absent /
/// not yet marked.
class AttendanceRing extends StatelessWidget {
  const AttendanceRing({
    super.key,
    required this.present,
    required this.half,
    required this.absent,
    required this.unmarked,
    this.size = 120,
    this.track = const Color(0x33FFFFFF),
    this.center,
  });

  final int present, half, absent, unmarked;
  final double size;
  final Color track;
  final Widget? center;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: size,
      height: size,
      child: Stack(
        alignment: Alignment.center,
        children: [
          CustomPaint(
            size: Size.square(size),
            painter: _RingPainter(present, half, absent, unmarked, track),
          ),
          ?center,
        ],
      ),
    );
  }
}

class _RingPainter extends CustomPainter {
  _RingPainter(this.p, this.h, this.a, this.u, this.track);
  final int p, h, a, u;
  final Color track;

  @override
  void paint(Canvas canvas, Size size) {
    const stroke = 14.0;
    final rect = Rect.fromLTWH(stroke / 2, stroke / 2, size.width - stroke, size.height - stroke);
    final base = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = stroke
      ..color = track;
    canvas.drawArc(rect, 0, math.pi * 2, false, base);
    final total = p + h + a + u;
    if (total == 0) return;
    // Light stroke colours chosen to read on the teal hero card.
    final parts = [
      (p, const Color(0xFF4ADE80)),
      (h, const Color(0xFFFBBF24)),
      (a, const Color(0xFFF87171)),
    ];
    var start = -math.pi / 2;
    const gap = 0.05;
    for (final (count, color) in parts) {
      if (count == 0) continue;
      final sweep = (count / total) * math.pi * 2;
      final paint = Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = stroke
        ..strokeCap = StrokeCap.round
        ..color = color;
      final s = math.max(sweep - gap, 0.02);
      canvas.drawArc(rect, start + gap / 2, s, false, paint);
      start += sweep;
    }
  }

  @override
  bool shouldRepaint(_RingPainter o) => o.p != p || o.h != h || o.a != a || o.u != u;
}

/// Seven-day grouped bars: billing (what we earn) next to labour cost.
class WeekChart extends StatefulWidget {
  const WeekChart({super.key, required this.points});
  final List<DayPoint> points;

  @override
  State<WeekChart> createState() => _WeekChartState();
}

class _WeekChartState extends State<WeekChart> {
  int? _selected;

  @override
  Widget build(BuildContext context) {
    final pts = widget.points;
    final maxV = pts.fold<int>(0, (m, p) => math.max(m, math.max(p.billing, p.cost)));
    final sel = _selected == null ? pts.last : pts[_selected!];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Wrap(
          spacing: 16,
          runSpacing: 4,
          children: [
            _Legend(ChartColors.billing, 'Billing'),
            _Legend(ChartColors.cost, 'Labour cost'),
          ],
        ),
        const SizedBox(height: 8),
        Row(
          children: [
            Expanded(
              child: Text(
                '${Money.format(sel.billing)}  /  ${Money.format(sel.cost)}',
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
                style: const TextStyle(fontWeight: FontWeight.w800, color: Palette.ink),
              ),
            ),
            Text(DateFormatShort.of(sel.date),
                style: const TextStyle(fontSize: 12, color: Palette.muted, fontWeight: FontWeight.w600)),
          ],
        ),
        const SizedBox(height: 10),
        SizedBox(
          height: 130,
          child: LayoutBuilder(builder: (context, c) {
            final slot = c.maxWidth / pts.length;
            return GestureDetector(
              behavior: HitTestBehavior.opaque,
              onTapDown: (d) => setState(
                  () => _selected = (d.localPosition.dx / slot).floor().clamp(0, pts.length - 1)),
              child: CustomPaint(
                size: Size(c.maxWidth, 130),
                painter: _BarsPainter(pts, maxV, _selected ?? pts.length - 1),
              ),
            );
          }),
        ),
      ],
    );
  }
}

class _Legend extends StatelessWidget {
  const _Legend(this.color, this.label);
  final Color color;
  final String label;
  @override
  Widget build(BuildContext context) => Row(children: [
        Container(
          width: 10,
          height: 10,
          decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(3)),
        ),
        const SizedBox(width: 6),
        Text(label, style: const TextStyle(fontSize: 12, color: Palette.muted, fontWeight: FontWeight.w600)),
      ]);
}

class _BarsPainter extends CustomPainter {
  _BarsPainter(this.pts, this.maxV, this.selected);
  final List<DayPoint> pts;
  final int maxV;
  final int selected;

  @override
  void paint(Canvas canvas, Size size) {
    const labelH = 18.0;
    final chartH = size.height - labelH;
    final slot = size.width / pts.length;
    final barW = math.min(slot * 0.26, 14.0);
    final grid = Paint()
      ..color = Palette.line
      ..strokeWidth = 1;
    canvas.drawLine(Offset(0, chartH), Offset(size.width, chartH), grid);
    canvas.drawLine(Offset(0, chartH / 2), Offset(size.width, chartH / 2),
        grid..color = Palette.line.withValues(alpha: 0.6));

    for (var i = 0; i < pts.length; i++) {
      final cx = slot * i + slot / 2;
      final isSel = i == selected;
      if (isSel) {
        canvas.drawRRect(
          RRect.fromRectAndRadius(
              Rect.fromLTWH(slot * i + 3, 0, slot - 6, size.height), const Radius.circular(8)),
          Paint()..color = Palette.brandTint.withValues(alpha: 0.7),
        );
      }
      void bar(double x, int v, Color color) {
        if (maxV == 0 || v <= 0) return;
        final h = math.max(chartH * v / maxV, 3.0);
        canvas.drawRRect(
          RRect.fromRectAndCorners(
            Rect.fromLTWH(x, chartH - h, barW, h),
            topLeft: const Radius.circular(4),
            topRight: const Radius.circular(4),
          ),
          Paint()..color = color,
        );
      }

      bar(cx - barW - 1, pts[i].billing, ChartColors.billing);
      bar(cx + 1, pts[i].cost, ChartColors.cost);

      final tp = TextPainter(
        text: TextSpan(
          text: '${D.weekday(pts[i].date).substring(0, 1)}${pts[i].date.day}',
          style: TextStyle(
            fontSize: 11,
            fontWeight: isSel ? FontWeight.w800 : FontWeight.w500,
            color: isSel ? Palette.ink : Palette.muted,
            fontFamily: 'NotoSans',
          ),
        ),
        textDirection: TextDirection.ltr,
      )..layout();
      tp.paint(canvas, Offset(cx - tp.width / 2, chartH + 4));
    }
  }

  @override
  bool shouldRepaint(_BarsPainter o) => o.pts != pts || o.selected != selected;
}

/// A coloured KPI tile.
class KpiTile extends StatelessWidget {
  const KpiTile({
    super.key,
    required this.label,
    required this.value,
    required this.icon,
    required this.color,
    this.hint,
    this.onTap,
  });

  final String label;
  final String value;
  final String? hint;
  final IconData icon;
  final Color color;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Palette.surface,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: Palette.line),
      ),
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(children: [
                Container(
                  width: 30,
                  height: 30,
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(9),
                  ),
                  child: Icon(icon, size: 17, color: color),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(label,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(fontSize: 12.5, color: Palette.muted, fontWeight: FontWeight.w600)),
                ),
              ]),
              const SizedBox(height: 10),
              FittedBox(
                fit: BoxFit.scaleDown,
                alignment: Alignment.centerLeft,
                child: Text(value,
                    style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w800, height: 1.1)),
              ),
              if (hint != null) ...[
                const SizedBox(height: 3),
                Text(hint!, style: const TextStyle(fontSize: 11.5, color: Palette.muted)),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

class DateFormatShort {
  DateFormatShort._();
  static String of(DateTime d) => '${D.weekday(d)} ${D.showShort(D.ymd(d))}';
}
