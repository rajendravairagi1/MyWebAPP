import 'package:intl/intl.dart';

import 'i18n.dart';

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

  static const _monthsShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  static const _monthsLong = [
    'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
  ];
  static const _days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

  static String _dd(DateTime d) => d.day.toString().padLeft(2, '0');

  /// 07 Oct 2026, with the month in the chosen app language.
  static String show(String ymdStr) => showDt(parse(ymdStr));
  static String showDt(DateTime d) => '${_dd(d)} ${tr(_monthsShort[d.month - 1])} ${d.year}';
  static String showShort(String ymdStr) {
    final d = parse(ymdStr);
    return '${_dd(d)} ${tr(_monthsShort[d.month - 1])}';
  }

  static String monthTitle(DateTime d) => '${tr(_monthsLong[d.month - 1])} ${d.year}';
  static String weekday(DateTime d) => tr(_days[d.weekday - 1]);

  /// The same formats in English only. PDFs use these.
  static String showEn(String ymdStr) => _show.format(parse(ymdStr));
  static String showDtEn(DateTime d) => _show.format(d);
  static String showShortEn(String ymdStr) => _showShort.format(parse(ymdStr));
  static String monthTitleEn(DateTime d) => _month.format(d);
  static String weekdayEn(DateTime d) => _weekday.format(d);

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

  /// Financial year label for a date, e.g. 2026-10-07 -> "2026-27" when the
  /// year starts in April, or "2026" when it starts in January.
  static String financialYear(DateTime d, {int startMonth = 4}) {
    if (startMonth == 1) return '${d.year}';
    final start = d.month >= startMonth ? d.year : d.year - 1;
    final end = (start + 1) % 100;
    return '$start-${end.toString().padLeft(2, '0')}';
  }
}
