import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:path/path.dart' as p;
import 'package:path_provider/path_provider.dart';
import 'package:share_plus/share_plus.dart';

import '../../core/dates.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../state/providers.dart';

/// Exports a consistent copy of the whole database so nothing is lost if the
/// phone is replaced. (Automatic cloud backup arrives with sync.)
class BackupScreen extends ConsumerStatefulWidget {
  const BackupScreen({super.key});

  @override
  ConsumerState<BackupScreen> createState() => _BackupScreenState();
}

class _BackupScreenState extends ConsumerState<BackupScreen> {
  bool _busy = false;

  Future<void> _export() async {
    setState(() => _busy = true);
    try {
      final tmp = await getTemporaryDirectory();
      final path = p.join(tmp.path, 'labourbook_backup_${D.today()}.sqlite');
      final f = File(path);
      if (await f.exists()) await f.delete();
      await ref.read(databaseProvider).customStatement('VACUUM INTO ?', [path]);
      await SharePlus.instance.share(ShareParams(
        files: [XFile(path)],
        subject: 'LabourBook backup ${D.today()}',
        text: 'LabourBook backup. Keep this file safe: it contains all labour and payment data.',
      ));
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Backup & export')),
      body: ListView(padding: const EdgeInsets.all(16), children: [
        AppCard(
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            const Icon(Icons.cloud_download_outlined, size: 32, color: Palette.brand),
            const SizedBox(height: 12),
            const Text('Save a copy of your data', style: TextStyle(fontSize: 17, fontWeight: FontWeight.w800)),
            const SizedBox(height: 6),
            const Text(
              'Creates one backup file with every labourer, attendance, payment, company and invoice. Send it to yourself on WhatsApp or save it to Drive.',
              style: TextStyle(color: Palette.muted, height: 1.45),
            ),
            const SizedBox(height: 16),
            FilledButton.icon(
              onPressed: _busy ? null : _export,
              icon: const Icon(Icons.ios_share),
              label: const Text('Create & share backup'),
            ),
          ]),
        ),
        const SizedBox(height: 12),
        const AppCard(
          color: Palette.halfBg,
          padding: EdgeInsets.all(12),
          child: Text(
            'Until cloud sync is connected, this phone holds the only copy. Back up regularly, and before changing or resetting your phone.',
            style: TextStyle(color: Palette.half, fontWeight: FontWeight.w600, fontSize: 13, height: 1.4),
          ),
        ),
      ]),
    );
  }
}
