import 'countries.dart';

/// Mobile number rules. India needs exactly 10 digits; elsewhere 7–15.
class Phone {
  Phone._();

  static int maxLength(Country c) => c.code == 'IN' ? 10 : 15;

  static String? validate(String? v, Country c, {bool required = false}) {
    final s = v ?? '';
    if (s.isEmpty) return required ? 'Enter the mobile number' : null;
    if (c.code == 'IN') return s.length == 10 ? null : 'Enter a 10-digit mobile number';
    return s.length >= 7 && s.length <= 15 ? null : 'Enter a valid mobile number';
  }

  static String prefix(Country c) => '${c.dial}  ';
}
