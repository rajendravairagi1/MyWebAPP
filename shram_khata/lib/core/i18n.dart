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
