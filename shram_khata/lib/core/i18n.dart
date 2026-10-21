import 'dart:convert';

import 'package:flutter/services.dart' show rootBundle;

class AppLang {
  const AppLang(this.code, this.native, this.english);
  final String code;
  final String native;
  final String english;
}

/// Screen text is written in English in the code and looked up here.
/// A missing translation simply shows the English text.
class I18n {
  I18n._();

  static const languages = <AppLang>[
    AppLang('en', 'English', 'English'),
    AppLang('hi', 'हिन्दी', 'Hindi'),
    AppLang('gu', 'ગુજરાતી', 'Gujarati'),
    AppLang('mr', 'मराठी', 'Marathi'),
    AppLang('kn', 'ಕನ್ನಡ', 'Kannada'),
    AppLang('ta', 'தமிழ்', 'Tamil'),
    AppLang('te', 'తెలుగు', 'Telugu'),
    AppLang('bn', 'বাংলা', 'Bengali'),
    AppLang('pa', 'ਪੰਜਾਬੀ', 'Punjabi'),
    AppLang('ml', 'മലയാളം', 'Malayalam'),
  ];

  static String current = 'en';
  static Map<String, String> _table = const {};

  static Future<void> load(String code) async {
    current = code;
    if (code == 'en') {
      _table = const {};
      return;
    }
    try {
      final raw = await rootBundle.loadString('assets/i18n/$code.json');
      _table = (jsonDecode(raw) as Map<String, dynamic>).map((k, v) => MapEntry(k, v as String));
    } catch (_) {
      _table = const {};
    }
  }

  static String tr(String s) => _table[s] ?? s;
}

/// Translate a piece of screen text.
String tr(String s) => I18n.tr(s);

/// Translate a sentence that contains values. Write the English with named
/// `{placeholders}`; the translation keeps the same names, so word order can
/// change per language: `trf('{n} marked present', {'n': '3'})`.
String trf(String template, Map<String, Object?> values) {
  var out = I18n.tr(template);
  values.forEach((k, v) => out = out.replaceAll('{$k}', '$v'));
  return out;
}
