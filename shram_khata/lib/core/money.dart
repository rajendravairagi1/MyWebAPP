import 'package:intl/intl.dart';

import 'countries.dart';

/// All money in the app is integer minor units (paise / cents). These helpers
/// convert for display and input using the currency chosen in Business
/// settings (symbol, digit grouping, amount in words).
class Money {
  Money._();

  static CurrencyInfo _c = Currencies.inr;

  static CurrencyInfo get current => _c;
  static String get symbol => _c.symbol;
  static String get code => _c.code;

  /// Called whenever the business currency is loaded or changed.
  static void configure(CurrencyInfo c) => _c = c;
  static void configureCode(String? code) => _c = Currencies.byCode(code);

  static String _num(double v, int decimals) => NumberFormat.decimalPatternDigits(
        locale: _c.indianGrouping ? 'en_IN' : 'en_US',
        decimalDigits: decimals,
      ).format(v);

  static String _sym(bool symbolOn) =>
      !symbolOn ? '' : (_c.spaced ? '${_c.symbol} ' : _c.symbol);

  static String format(int minor, {bool symbol = true}) {
    final whole = minor % 100 == 0;
    final body = _num(minor.abs() / 100, whole ? 0 : 2);
    return '${minor < 0 ? '-' : ''}${_sym(symbol)}$body';
  }

  /// Compact form for dashboard tiles: 1.2L / 3.4Cr (Indian) or 1.2M / 12.5K.
  static String compact(int minor) {
    final r = minor.abs() / 100;
    final sign = minor < 0 ? '-' : '';
    String s;
    if (_c.indianGrouping) {
      if (r >= 10000000) {
        s = '${_trim(r / 10000000)}Cr';
      } else if (r >= 100000) {
        s = '${_trim(r / 100000)}L';
      } else if (r >= 10000) {
        s = '${_trim(r / 1000)}K';
      } else {
        return format(minor);
      }
    } else {
      if (r >= 1000000000) {
        s = '${_trim(r / 1000000000)}B';
      } else if (r >= 1000000) {
        s = '${_trim(r / 1000000)}M';
      } else if (r >= 10000) {
        s = '${_trim(r / 1000)}K';
      } else {
        return format(minor);
      }
    }
    return '$sign${_sym(true)}$s';
  }

  static String _trim(double v) {
    final s = v.toStringAsFixed(2);
    return s.replaceFirst(RegExp(r'\.?0+$'), '');
  }

  /// Parses user input such as "1,250", "450.50" or "$ 12" into minor units.
  /// Null if invalid.
  static int? parse(String input) {
    final cleaned = input.replaceAll(RegExp(r'[^0-9.\-]'), '');
    if (cleaned.isEmpty) return null;
    final v = double.tryParse(cleaned);
    if (v == null || v.isNaN || v.isInfinite) return null;
    return (v * 100).round();
  }

  /// Plain editable text for a form field (no symbol, no grouping).
  static String toInput(int minor) {
    if (minor % 100 == 0) return (minor ~/ 100).toString();
    return (minor / 100).toStringAsFixed(2);
  }

  /// "Rupees One Lakh Twenty Thousand Only" (or "US Dollars One Thousand Only"),
  /// using lakh / crore for Indian-style currencies and million / billion
  /// otherwise.
  static String inWords(int minor) {
    final negative = minor < 0;
    final abs = minor.abs();
    final whole = abs ~/ 100;
    final cents = abs % 100;
    final b = StringBuffer();
    if (negative) b.write('Minus ');
    b.write('${_c.major} ${_words(whole)}');
    if (cents > 0) b.write(' and ${_below100(cents)} ${_c.minor}');
    b.write(' Only');
    return b.toString();
  }

  static const _ones = [
    'Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight',
    'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen',
    'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen',
  ];
  static const _tens = [
    '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy',
    'Eighty', 'Ninety',
  ];

  static String _below100(int n) {
    if (n < 20) return _ones[n];
    final t = _tens[n ~/ 10];
    return n % 10 == 0 ? t : '$t ${_ones[n % 10]}';
  }

  static String _below1000(int n) {
    if (n < 100) return _below100(n);
    final h = '${_ones[n ~/ 100]} Hundred';
    return n % 100 == 0 ? h : '$h ${_below100(n % 100)}';
  }

  static String _words(int n) {
    if (n == 0) return 'Zero';
    final parts = <String>[];
    if (_c.indianGrouping) {
      final crore = n ~/ 10000000;
      n %= 10000000;
      final lakh = n ~/ 100000;
      n %= 100000;
      final thousand = n ~/ 1000;
      n %= 1000;
      if (crore > 0) parts.add('${_words(crore)} Crore');
      if (lakh > 0) parts.add('${_below100(lakh)} Lakh');
      if (thousand > 0) parts.add('${_below100(thousand)} Thousand');
      if (n > 0) parts.add(_below1000(n));
    } else {
      const units = [(1000000000, 'Billion'), (1000000, 'Million'), (1000, 'Thousand')];
      for (final (size, name) in units) {
        final q = n ~/ size;
        if (q > 0) parts.add('${_below1000(q)} $name');
        n %= size;
      }
      if (n > 0) parts.add(_below1000(n));
    }
    return parts.join(' ');
  }
}

/// Days / hours shown without a trailing ".0" (22, 22.5).
String num1(double v) {
  if (v == v.roundToDouble()) return v.round().toString();
  return v.toStringAsFixed(1);
}
