import 'package:intl/intl.dart';

/// All money in the app is integer paise. These helpers convert for display
/// and input, using Indian digit grouping (12,34,567).
class Money {
  Money._();

  static String format(int paise, {bool symbol = true}) {
    final whole = paise % 100 == 0;
    final f = NumberFormat.currency(
      locale: 'en_IN',
      symbol: symbol ? '₹' : '',
      decimalDigits: whole ? 0 : 2,
    );
    return f.format(paise / 100).trim();
  }

  /// Compact form for dashboard tiles: ₹1.2L, ₹3.4Cr, ₹12.5K.
  static String compact(int paise) {
    final r = paise.abs() / 100;
    final sign = paise < 0 ? '-' : '';
    String s;
    if (r >= 10000000) {
      s = '${_trim(r / 10000000)}Cr';
    } else if (r >= 100000) {
      s = '${_trim(r / 100000)}L';
    } else if (r >= 10000) {
      s = '${_trim(r / 1000)}K';
    } else {
      return format(paise);
    }
    return '$sign₹$s';
  }

  static String _trim(double v) {
    final s = v.toStringAsFixed(2);
    return s.replaceFirst(RegExp(r'\.?0+$'), '');
  }

  /// Parses user input such as "1,250", "₹ 450.50" into paise. Null if invalid.
  static int? parse(String input) {
    final cleaned = input.replaceAll(RegExp(r'[₹,\s]'), '');
    if (cleaned.isEmpty) return null;
    final v = double.tryParse(cleaned);
    if (v == null || v.isNaN || v.isInfinite) return null;
    return (v * 100).round();
  }

  /// Plain editable text for a form field (no symbol, no grouping).
  static String toInput(int paise) {
    if (paise % 100 == 0) return (paise ~/ 100).toString();
    return (paise / 100).toStringAsFixed(2);
  }

  /// "Rupees One Lakh Twenty Thousand Only" in the Indian numbering system.
  static String inWords(int paise) {
    final negative = paise < 0;
    final abs = paise.abs();
    final rupees = abs ~/ 100;
    final ps = abs % 100;
    final b = StringBuffer();
    if (negative) b.write('Minus ');
    b.write('Rupees ${_words(rupees)}');
    if (ps > 0) b.write(' and ${_words(ps)} Paise');
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
    return parts.join(' ');
  }
}

/// Days / hours shown without a trailing ".0" (22, 22.5).
String num1(double v) {
  if (v == v.roundToDouble()) return v.round().toString();
  return v.toStringAsFixed(1);
}
