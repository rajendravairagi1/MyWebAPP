import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../state/providers.dart';

/// Pick the app language. Shown first on a fresh install and from More.
class LanguageScreen extends ConsumerWidget {
  const LanguageScreen({super.key, this.first = false});

  /// True on the very first launch: no back button, picking continues.
  final bool first;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final current = ref.watch(languageProvider);
    return Scaffold(
      appBar: AppBar(
        automaticallyImplyLeading: !first,
        title: Text(tr('Choose language / भाषा चुनें')),
      ),
      bottomNavigationBar: first
          ? SafeArea(
              child: Padding(
                padding: const EdgeInsets.fromLTRB(16, 8, 16, 12),
                child: FilledButton(
                  onPressed: () => ref.read(languageProvider.notifier).set(current),
                  child: Text(tr('Continue')),
                ),
              ),
            )
          : null,
      body: ListView(
        padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
        children: [
          for (final l in I18n.languages)
            Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: Material(
                color: current == l.code ? Palette.brandSoft : Palette.surface,
                borderRadius: BorderRadius.circular(14),
                child: InkWell(
                  borderRadius: BorderRadius.circular(14),
                  onTap: () async {
                    if (first) {
                      // Pick, see the screen change, then press Continue.
                      await ref.read(languageProvider.notifier).preview(l.code);
                    } else {
                      await ref.read(languageProvider.notifier).set(l.code);
                      if (context.mounted) Navigator.pop(context);
                    }
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: current == l.code ? Palette.brand : Palette.line),
                    ),
                    child: Row(children: [
                      Expanded(
                        child: Text(l.native,
                            style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700)),
                      ),
                      if (l.code != 'en')
                        Text(l.english, style: const TextStyle(color: Palette.muted)),
                      if (current == l.code) ...[
                        const SizedBox(width: 10),
                        Icon(Icons.check_circle, color: Palette.brand),
                      ],
                    ]),
                  ),
                ),
              ),
            ),
        ],
      ),
    );
  }
}
