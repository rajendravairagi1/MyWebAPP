import 'package:intl/intl.dart';

/// Date-only values are persisted as `yyyy-MM-dd` text.
class D {
  D._();

  static final _ymd = DateFormat('yyyy-MM-dd');
  static final _show = DateFormat('dd MMM yyyy');
  static final _showShort = DateFormat('dd MMM');
  static final _month = DateFormat('MMMM yyyy');
  static final _weekday = DateFormat('EEE');

  static String ymd(DateTime d) => _ymd.format(d);
  static DateTime parse(String s) => _ymd.parseStrict(s);
  static DateTime? tryParse(String? s) {
    if (s == null || s.isEmpty) return null;
    try {
      return _ymd.parseStrict(s);
    } catch (_) {
      return null;
    }
  }

  static DateTime dateOnly(DateTime d) => DateTime(d.year, d.month, d.day);
  static String today() => ymd(DateTime.now());

  /// 07 Oct 2026
  static String show(String ymdStr) => _show.format(parse(ymdStr));
  static String showDt(DateTime d) => _show.format(d);
  static String showShort(String ymdStr) => _showShort.format(parse(ymdStr));
  static String monthTitle(DateTime d) => _month.format(d);
  static String weekday(DateTime d) => _weekday.format(d);

  static DateTime firstOfMonth(DateTime d) => DateTime(d.year, d.month, 1);
  static DateTime lastOfMonth(DateTime d) => DateTime(d.year, d.month + 1, 0);
  static int daysInMonth(DateTime d) => lastOfMonth(d).day;

  static DateTime addDays(DateTime d, int n) =>
      DateTime(d.year, d.month, d.day + n);

  static bool isToday(DateTime d) => ymd(d) == today();

  /// Inclusive list of dates from [from] to [to].
  static List<DateTime> range(DateTime from, DateTime to) {
    final out = <DateTime>[];
    var d = dateOnly(from);
    final end = dateOnly(to);
    while (!d.isAfter(end)) {
      out.add(d);
      d = addDays(d, 1);
    }
    return out;
  }

  /// Indian financial year label for a date, e.g. 2026-10-07 -> "2026-27".
  static String financialYear(DateTime d) {
    final start = d.month >= 4 ? d.year : d.year - 1;
    final end = (start + 1) % 100;
    return '$start-${end.toString().padLeft(2, '0')}';
  }
}
