import 'dart:async';

import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../data/sync/api_client.dart';
import '../data/sync/sync_service.dart';
import 'providers.dart';

final syncServiceProvider = Provider<SyncService>(
    (ref) => SyncService(ref.watch(databaseProvider), ref.watch(prefsProvider)));

class CloudState {
  const CloudState({
    this.session,
    this.syncing = false,
    this.lastSync,
    this.error,
    this.unsent = 0,
    this.unread = 0,
  });

  final CloudSession? session;
  final bool syncing;
  final DateTime? lastSync;

  /// Plain-language reason the last sync failed (offline, signed out ...).
  final String? error;

  /// Changes on this phone that are not on the server yet.
  final int unsent;

  /// Messages from company managers nobody has opened yet.
  final int unread;

  bool get connected => session != null;

  CloudState copyWith({
    Object? session = _keep,
    bool? syncing,
    Object? lastSync = _keep,
    Object? error = _keep,
    int? unsent,
    int? unread,
  }) =>
      CloudState(
        session: identical(session, _keep) ? this.session : session as CloudSession?,
        syncing: syncing ?? this.syncing,
        lastSync: identical(lastSync, _keep) ? this.lastSync : lastSync as DateTime?,
        error: identical(error, _keep) ? this.error : error as String?,
        unsent: unsent ?? this.unsent,
        unread: unread ?? this.unread,
      );

  static const _keep = Object();
}

/// Keeps this phone in step with the cloud while the app is open.
class CloudNotifier extends Notifier<CloudState> {
  Timer? _timer;
  Timer? _debounce;
  DateTime _feedbackAt = DateTime.fromMillisecondsSinceEpoch(0);

  SyncService get _svc => ref.read(syncServiceProvider);

  @override
  CloudState build() {
    final svc = ref.read(syncServiceProvider);
    // Upload soon after anything changes on this phone.
    ref.listen(dbTickProvider, (_, _) => _scheduleUpload());
    _timer = Timer.periodic(const Duration(seconds: 60), (_) => syncNow());
    ref.onDispose(() {
      _timer?.cancel();
      _debounce?.cancel();
    });
    Future.microtask(() => syncNow());
    return CloudState(session: svc.session, lastSync: svc.lastSync);
  }

  void _scheduleUpload() {
    final s = state.session;
    if (s == null || s.isManager) return;
    _debounce?.cancel();
    _debounce = Timer(const Duration(seconds: 4), () async {
      if (await _svc.unsent() > 0) await syncNow();
    });
  }

  /// Called after signing in or out so screens see the new session.
  void refreshSession() {
    state = state.copyWith(session: _svc.session, error: null, lastSync: _svc.lastSync);
  }

  Future<void> syncNow() async {
    final s = _svc.session;
    if (s == null) {
      if (state.session != null) refreshSession();
      return;
    }
    if (state.syncing) return;
    state = state.copyWith(session: s, syncing: true, error: null);
    try {
      await _svc.sync();
      state = state.copyWith(syncing: false, lastSync: _svc.lastSync, unsent: await _svc.unsent());
      if (!s.isManager) await _refreshUnread();
    } on OfflineException catch (e) {
      state = state.copyWith(syncing: false, error: e.toString(), unsent: await _svc.unsent());
    } on ApiException catch (e) {
      state = state.copyWith(
          syncing: false,
          session: _svc.session,
          error: e.status == 401 ? 'Your cloud session ended. Please sign in again.' : e.message);
    } catch (e) {
      state = state.copyWith(syncing: false, error: '$e');
    }
  }

  Future<void> _refreshUnread({bool force = false}) async {
    if (!force && DateTime.now().difference(_feedbackAt) < const Duration(seconds: 55)) return;
    _feedbackAt = DateTime.now();
    try {
      final f = await _svc.feedback();
      state = state.copyWith(unread: f.unread);
    } catch (_) {}
  }

  Future<void> refreshUnread() => _refreshUnread(force: true);
}

final cloudProvider = NotifierProvider<CloudNotifier, CloudState>(CloudNotifier.new);
