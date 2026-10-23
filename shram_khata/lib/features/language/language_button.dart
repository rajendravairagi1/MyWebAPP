import 'package:flutter/material.dart';

import '../../core/i18n.dart';
import 'language_screen.dart';

/// "A  हिन्दी" style button that opens the language list.
class LanguageButton extends StatelessWidget {
  const LanguageButton({super.key, this.color});
  final Color? color;

  @override
  Widget build(BuildContext context) {
    final name = I18n.languages.firstWhere((l) => l.code == I18n.current).native;
    return TextButton.icon(
      style: TextButton.styleFrom(foregroundColor: color),
      onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LanguageScreen())),
      icon: const Icon(Icons.language, size: 20),
      label: Text(name),
    );
  }
}
