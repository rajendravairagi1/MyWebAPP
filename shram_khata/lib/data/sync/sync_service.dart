import 'dart:convert';

import 'package:shared_preferences/shared_preferences.dart';

import '../database.dart';
import 'api_client.dart';
import 'sync_store.dart';

/// Who is signed in to the cloud on this phone.
class CloudSession {
  CloudSession({
    required this.token,
    required this.userId,
    required this.agencyId,
    required this.kind,
    required this.name,
    this.scopeCompanyId,
    this.agencyName = '',
  });

  final String token;
  final String userId;
  final String agencyId;

  /// owner | staff | manager
  final String kind;
  final String name;
  final String agencyName;
  final String? scopeCompanyId;

  bool get isManager => kind == 'manager';
  bool get isOwner => kind == 'owner';

  Map<String, Object?> toJson() => {
        'token': token,
        'userId': userId,
        'agencyId': agencyId,
        'kind': kind,
        'name': name,
        'agencyName': agencyName,
        'scopeCompanyId': scopeCompanyId,
      };

  factory CloudSession.fromJson(Map<String, dynamic> j) => CloudSession(
        token: j['token'] as String,
        userId: j['userId'] as String,
        agencyId: j['agencyId'] as String,
        kind: j['kind'] as String,
        name: (j['name'] as String?) ?? '',
        agencyName: (j['agencyName'] as String?) ?? '',
        scopeCompanyId: j['scopeCompanyId'] as String?,
      );
}

class SyncReport {
  SyncReport({this.pushed = 0, this.pulled = 0});
  int pushed;
  int pulled;
}

class CloudPerson {
  CloudPerson({
    required this.id,
    required this.name,
    required this.email,
    required this.mobile,
    required this.kind,
    required this.isActive,
    this.scopeCompanyId,
  });
  final String id;
  final String name;
  final String? email;
  final String? mobile;
  final String kind;
  final bool isActive;
  final String? scopeCompanyId;
}

class FeedbackItem {
  FeedbackItem({
    required this.id,
    required this.fromName,
    required this.message,
    required this.createdAt,
    required this.isRead,
    this.companyId,
    this.labourId,
  });
  final String id;
  final String fromName;
  final String message;
  final DateTime createdAt;
  final bool isRead;
  final String? companyId;
  final String? labourId;
}

/// Talks to the cloud: sign-in, upload local changes, download others' changes.
class SyncService {
  SyncService(this.db, this.prefs, {this.baseUrl = defaultServerUrl})
      : store = SyncStore(db);

  final AppDatabase db;
  final SharedPreferences prefs;
  final SyncStore store;
  final String baseUrl;

  static const _kSession = 'cloud.session';
  static const _kSeq = 'cloud.seq';
  static const _kLast = 'cloud.lastSync';

  bool _running = false;

  CloudSession? get session {
    final raw = prefs.getString(_kSession);
    if (raw == null) return null;
    try {
      return CloudSession.fromJson((jsonDecode(raw) as Map).cast<String, dynamic>());
    } catch (_) {
      return null;
    }
  }

  DateTime? get lastSync {
    final v = prefs.getInt(_kLast);
    return v == null ? null : DateTime.fromMillisecondsSinceEpoch(v);
  }

  ApiClient _api({bool auth = true}) => ApiClient(baseUrl: baseUrl, token: auth ? session?.token : null);

  Future<void> _save(CloudSession s) => prefs.setString(_kSession, jsonEncode(s.toJson()));

  CloudSession _fromAuth(Map<String, dynamic> r) {
    final u = (r['user'] as Map).cast<String, dynamic>();
    return CloudSession(
      token: r['token'] as String,
      userId: u['id'] as String,
      agencyId: u['agencyId'] as String,
      kind: u['kind'] as String,
      name: (u['name'] as String?) ?? '',
      agencyName: (u['agencyName'] as String?) ?? '',
      scopeCompanyId: u['scopeCompanyId'] as String?,
    );
  }

  // ---- sign in / out -------------------------------------------------------------

  /// Turns the workspace on this phone into a cloud account and uploads it.
  Future<CloudSession> register({
    required String businessName,
    required String ownerName,
    required String email,
    required String mobile,
    required String password,
    required String ownerId,
    required String agencyId,
    String planId = 'solo',
    String device = '',
  }) async {
    final r = await _api(auth: false).post('/v1/auth/register', {
      'businessName': businessName,
      'ownerName': ownerName,
      'email': email,
      'mobile': mobile,
      'password': password,
      'planId': planId,
      'device': device,
      'userId': ownerId,
      'agencyId': agencyId,
    });
    final s = _fromAuth(r);
    await _save(s);
    await prefs.setInt(_kSeq, 0);
    await store.setEnabled(true);
    await store.enqueueAll();
    return s;
  }

  /// Signs in with an existing cloud account (owner, supervisor, manager).
  Future<CloudSession> login(String identifier, String password, {String device = ''}) async {
    final r = await _api(auth: false).post('/v1/auth/login', {
      'identifier': identifier,
      'password': password,
      'device': device,
    });
    final s = _fromAuth(r);
    await _save(s);
    await prefs.setInt(_kSeq, 0);
    // Managers only read; everyone else uploads what they change.
    await store.setEnabled(!s.isManager);
    return s;
  }

  /// Stops syncing on this phone. The data stays on the phone.
  Future<void> signOut() async {
    try {
      await _api().post('/v1/auth/logout');
    } catch (_) {}
    await prefs.remove(_kSession);
    await prefs.remove(_kSeq);
    await prefs.remove(_kLast);
    await store.reset();
  }

  /// Owner: erase the cloud copy of the agency. Phones keep their own data.
  Future<void> deleteCloudAccount(String password) async {
    await _api().post('/v1/account/delete', {'password': password, 'confirm': 'DELETE'});
    await prefs.remove(_kSession);
    await prefs.remove(_kSeq);
    await prefs.remove(_kLast);
    await store.reset();
  }

  /// Clears the saved sign-in without contacting the server (session expired).
  Future<void> forgetSession() async {
    await prefs.remove(_kSession);
    await store.setEnabled(false);
  }

  // ---- sync ----------------------------------------------------------------------

  Future<int> unsent() => store.pendingCount();

  /// Uploads local changes, then downloads everyone else's. Safe to call often.
  Future<SyncReport?> sync() async {
    final s = session;
    if (s == null || _running) return null;
    _running = true;
    final report = SyncReport();
    try {
      final api = _api();
      if (!s.isManager) {
        while (true) {
          final batch = await store.pending(limit: 200);
          if (batch.isEmpty) break;
          await api.post('/v1/sync/push', {'changes': [for (final c in batch.changes) c.toJson()]});
          await store.acknowledge(batch);
          report.pushed += batch.changes.length;
        }
      }
      var more = true;
      var guard = 0;
      while (more && guard++ < 200) {
        final since = prefs.getInt(_kSeq) ?? 0;
        final r = await api.get('/v1/sync/pull', query: {'since': '$since', 'limit': '${s.isManager ? 1000 : 500}'});
        final changes = [
          for (final c in (r['changes'] as List).cast<Map>())
            SyncChange(
              table: c['t'] as String,
              id: c['id'] as String,
              data: ((c['d'] as Map?) ?? const {}).cast<String, Object?>(),
              deleted: c['del'] == true,
            ),
        ];
        await store.apply(changes);
        report.pulled += changes.length;
        await prefs.setInt(_kSeq, (r['seq'] as num).toInt());
        more = r['more'] == true;
      }
      await prefs.setInt(_kLast, DateTime.now().millisecondsSinceEpoch);
      return report;
    } on ApiException catch (e) {
      if (e.status == 401) await forgetSession();
      rethrow;
    } finally {
      _running = false;
    }
  }

  // ---- password ----------------------------------------------------------------

  /// Asks the server to email a reset code. Always succeeds, so nobody can
  /// find out which accounts exist.
  Future<void> forgotPassword(String identifier) async {
    await _api(auth: false).post('/v1/auth/forgot', {'identifier': identifier});
  }

  Future<void> resetPassword(String identifier, String code, String password) async {
    await _api(auth: false).post('/v1/auth/reset', {'identifier': identifier, 'code': code, 'password': password});
  }

  /// Keeps the cloud password in step when it is changed on the phone.
  Future<void> changePassword(String current, String next) async {
    await _api().post('/v1/auth/password', {'current': current, 'new': next});
  }

  // ---- people ---------------------------------------------------------------------

  Future<List<CloudPerson>> people() async {
    final r = await _api().get('/v1/users');
    return [
      for (final u in (r['users'] as List).cast<Map>())
        CloudPerson(
          id: u['id'] as String,
          name: u['name'] as String,
          email: u['email'] as String?,
          mobile: u['mobile'] as String?,
          kind: u['kind'] as String,
          isActive: u['isActive'] == true,
          scopeCompanyId: u['scopeCompanyId'] as String?,
        ),
    ];
  }

  /// Creates a cloud login. [id] is the staff member's id so the two match.
  Future<void> createPerson({
    required String id,
    required String name,
    required String kind,
    required String password,
    String email = '',
    String mobile = '',
    String? scopeCompanyId,
  }) async {
    await _api().post('/v1/users', {
      'id': id,
      'name': name,
      'kind': kind,
      'password': password,
      'email': email,
      'mobile': mobile,
      'scopeCompanyId': ?scopeCompanyId,
    });
  }

  Future<void> updatePerson(String id, {bool? isActive, String? password}) async {
    await _api().post('/v1/users/$id', {'isActive': ?isActive, 'password': ?password});
  }

  // ---- feedback ----------------------------------------------------------------------

  Future<void> sendFeedback(String message, {String? labourId}) async {
    await _api().post('/v1/feedback', {'message': message, 'labourId': ?labourId});
  }

  Future<({List<FeedbackItem> items, int unread})> feedback() async {
    final r = await _api().get('/v1/feedback');
    final items = [
      for (final f in (r['items'] as List).cast<Map>())
        FeedbackItem(
          id: f['id'] as String,
          fromName: (f['fromName'] as String?) ?? '',
          message: f['message'] as String,
          createdAt: DateTime.tryParse('${f['createdAt']}')?.toLocal() ?? DateTime.now(),
          isRead: f['isRead'] == true,
          companyId: f['companyId'] as String?,
          labourId: f['labourId'] as String?,
        ),
    ];
    return (items: items, unread: (r['unread'] as num?)?.toInt() ?? 0);
  }

  Future<void> markFeedbackRead(List<String> ids) async {
    if (ids.isEmpty) return;
    await _api().post('/v1/feedback/read', {'ids': ids});
  }
}
