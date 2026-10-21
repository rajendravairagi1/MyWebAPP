import 'dart:convert';
import 'dart:io';

import 'package:flutter_test/flutter_test.dart';
import 'package:shram_khata/core/i18n.dart';

List<String> _placeholders(String s) =>
    (RegExp(r'\{[a-zA-Z_]+\}').allMatches(s).map((m) => m.group(0)!).toList()..sort());

void main() {
  // The Hindi file is the reference for which texts exist.
  Map<String, dynamic> load(String code) =>
      jsonDecode(File('assets/i18n/$code.json').readAsStringSync()) as Map<String, dynamic>;

  final reference = load('hi').keys.toSet();

  for (final lang in I18n.languages.where((l) => l.code != 'en')) {
    test('${lang.english}: every text is translated and placeholders are kept', () {
      final t = load(lang.code);
      expect(t.keys.toSet().difference(reference), isEmpty, reason: 'unknown keys');
      expect(reference.difference(t.keys.toSet()), isEmpty, reason: 'missing translations');
      for (final e in t.entries) {
        expect((e.value as String).trim(), isNotEmpty, reason: e.key);
        expect(_placeholders(e.value as String), _placeholders(e.key), reason: e.key);
      }
    });
  }

  test('trf fills placeholders and falls back to English', () {
    I18n.current = 'en';
    expect(trf('{n} marked present', {'n': 3}), '3 marked present');
    expect(tr('Some text nobody translated'), 'Some text nobody translated');
  });
}
