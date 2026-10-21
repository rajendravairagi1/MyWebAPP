import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/database.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';
import '../cloud/feedback_screen.dart';

final _managerLabourProvider = FutureProvider.autoDispose<List<Labour>>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(labourServiceProvider).list();
});

/// Write to the agency (owner and supervisors are notified) and see past messages.
class ManagerFeedback extends ConsumerStatefulWidget {
  const ManagerFeedback({super.key});

  @override
  ConsumerState<ManagerFeedback> createState() => _ManagerFeedbackState();
}

class _ManagerFeedbackState extends ConsumerState<ManagerFeedback> {
  final _text = TextEditingController();
  String? _labourId;
  bool _busy = false;

  @override
  void dispose() {
    _text.dispose();
    super.dispose();
  }

  Future<void> _send() async {
    final msg = _text.text.trim();
    if (msg.isEmpty) return;
    setState(() => _busy = true);
    try {
      await ref.read(syncServiceProvider).sendFeedback(msg, labourId: _labourId);
      _text.clear();
      setState(() => _labourId = null);
      ref.invalidate(feedbackProvider);
      if (mounted) showInfo(context, tr('Message sent to the agency.'));
    } catch (e) {
      if (mounted) showError(context, e);
    } finally {
      if (mounted) setState(() => _busy = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final labours = ref.watch(_managerLabourProvider).value ?? const <Labour>[];
    final mine = ref.watch(feedbackProvider);
    return ListView(padding: const EdgeInsets.fromLTRB(16, 12, 16, 24), children: [
      Text(tr('Tell the agency about a worker or a problem. The owner and supervisors get your message.'),
          style: const TextStyle(color: Palette.muted)),
      const SizedBox(height: 12),
      DropdownButtonFormField<String?>(
        initialValue: _labourId,
        isExpanded: true,
        decoration: InputDecoration(labelText: tr('About a worker (optional)')),
        items: [
          DropdownMenuItem(value: null, child: Text(tr('General'))),
          for (final l in labours) DropdownMenuItem(value: l.id, child: Text(l.name)),
        ],
        onChanged: (v) => setState(() => _labourId = v),
      ),
      const SizedBox(height: 12),
      TextField(
        controller: _text,
        maxLines: 4,
        maxLength: 2000,
        decoration: InputDecoration(labelText: tr('Your message')),
      ),
      const SizedBox(height: 8),
      FilledButton.icon(
        onPressed: _busy ? null : _send,
        icon: const Icon(Icons.send),
        label: Text(tr('Send to agency')),
      ),
      SectionTitle(tr('YOUR EARLIER MESSAGES'), padding: const EdgeInsets.fromLTRB(4, 22, 4, 8)),
      mine.when(
        loading: () => const Padding(padding: EdgeInsets.all(20), child: LoadingView()),
        error: (e, _) => Text(tr(e.toString()), style: const TextStyle(color: Palette.muted)),
        data: (v) => v.items.isEmpty
            ? Text(tr('No messages yet'), style: const TextStyle(color: Palette.muted))
            : Column(children: [
                for (final i in v.items)
                  Padding(
                    padding: const EdgeInsets.only(bottom: 8),
                    child: AppCard(
                      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                        Text(D.showDt(i.createdAt), style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
                        const SizedBox(height: 4),
                        Text(i.message),
                      ]),
                    ),
                  ),
              ]),
      ),
    ]);
  }
}
