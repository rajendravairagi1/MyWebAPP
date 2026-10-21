import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../core/dates.dart';
import '../../core/i18n.dart';
import '../../core/theme.dart';
import '../../core/widgets.dart';
import '../../data/sync/sync_service.dart';
import '../../state/cloud.dart';
import '../../state/providers.dart';

final feedbackProvider = FutureProvider.autoDispose<({List<FeedbackItem> items, int unread})>((ref) async {
  return ref.watch(syncServiceProvider).feedback();
});

/// Messages written by company managers (owner and supervisors read them).
class FeedbackScreen extends ConsumerStatefulWidget {
  const FeedbackScreen({super.key});

  @override
  ConsumerState<FeedbackScreen> createState() => _FeedbackScreenState();
}

class _FeedbackScreenState extends ConsumerState<FeedbackScreen> {
  Future<void> _markRead(List<FeedbackItem> items) async {
    final unread = [for (final i in items) if (!i.isRead) i.id];
    if (unread.isEmpty) return;
    try {
      await ref.read(syncServiceProvider).markFeedbackRead(unread);
      await ref.read(cloudProvider.notifier).refreshUnread();
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final data = ref.watch(feedbackProvider);
    ref.listen(feedbackProvider, (_, next) {
      final v = next.value;
      if (v != null) _markRead(v.items);
    });
    return Scaffold(
      appBar: AppBar(title: Text(tr('Messages from companies'))),
      body: data.when(
        loading: () => const LoadingView(),
        error: (e, _) => ErrorView(e),
        data: (v) {
          if (v.items.isEmpty) {
            return EmptyState(
              icon: Icons.forum_outlined,
              title: tr('No messages yet'),
              message: tr('When a company manager sends feedback about your workers, it shows up here.'),
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(feedbackProvider),
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
              itemCount: v.items.length,
              separatorBuilder: (_, _) => const SizedBox(height: 8),
              itemBuilder: (_, i) => _Message(item: v.items[i]),
            ),
          );
        },
      ),
    );
  }
}

class _Message extends ConsumerWidget {
  const _Message({required this.item});
  final FeedbackItem item;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return FutureBuilder<String?>(
      future: _labourName(ref),
      builder: (context, snap) => AppCard(
        color: item.isRead ? null : Palette.brandTint,
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [
            Expanded(child: Text(item.fromName, style: const TextStyle(fontWeight: FontWeight.w800))),
            Text(D.showDt(item.createdAt), style: const TextStyle(color: Palette.muted, fontSize: 12.5)),
          ]),
          if (snap.data != null) ...[
            const SizedBox(height: 4),
            Pill(trf('About {name}', {'name': snap.data}), color: Palette.info),
          ],
          const SizedBox(height: 8),
          Text(item.message, style: const TextStyle(height: 1.4)),
        ]),
      ),
    );
  }

  Future<String?> _labourName(WidgetRef ref) async {
    final id = item.labourId;
    if (id == null) return null;
    return (await ref.read(labourServiceProvider).byId(id))?.name;
  }
}
