import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../core/dates.dart';
import '../core/permissions.dart';
import '../core/plans.dart';
import '../data/database.dart';
import '../data/services/attendance_service.dart';
import '../data/services/billing_service.dart';
import '../data/services/company_service.dart';
import '../data/services/dashboard_service.dart';
import '../data/services/labour_service.dart';
import '../data/services/payment_service.dart';
import '../data/services/report_service.dart';
import '../data/services/workspace_service.dart';
import '../domain/business_profile.dart';

/// Overridden in main() with the opened on-device database.
final databaseProvider = Provider<AppDatabase>((ref) => throw UnimplementedError());

final prefsProvider = Provider<SharedPreferences>((ref) => throw UnimplementedError());

final workspaceServiceProvider = Provider((ref) => WorkspaceService(ref.watch(databaseProvider)));
final labourServiceProvider = Provider((ref) => LabourService(ref.watch(databaseProvider)));
final companyServiceProvider = Provider((ref) => CompanyService(ref.watch(databaseProvider)));
final attendanceServiceProvider = Provider((ref) => AttendanceService(ref.watch(databaseProvider)));
final paymentServiceProvider = Provider((ref) => PaymentService(ref.watch(databaseProvider)));
final billingServiceProvider = Provider((ref) => BillingService(ref.watch(databaseProvider)));
final dashboardServiceProvider = Provider((ref) => DashboardService(ref.watch(databaseProvider)));
final reportServiceProvider = Provider((ref) => ReportService(ref.watch(databaseProvider)));

/// Ticks whenever any table changes. Data providers watch it so every screen
/// refreshes after a write without manual invalidation.
final dbTickProvider = StreamProvider<int>((ref) async* {
  final db = ref.watch(databaseProvider);
  var n = 0;
  yield n;
  await for (final _ in db.tableUpdates()) {
    yield ++n;
  }
});

final profileProvider = FutureProvider<BusinessProfile>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(workspaceServiceProvider).profile();
});

final branchesProvider = FutureProvider<List<Branch>>((ref) async {
  ref.watch(dbTickProvider);
  return ref.watch(workspaceServiceProvider).branches();
});

final planProvider = Provider<Plan>((ref) {
  final p = ref.watch(profileProvider).value;
  return Plan.fromId(p?.planId);
});

// ---- session -----------------------------------------------------------------

class SessionState {
  const SessionState({
    this.ready = false,
    this.hasWorkspace = false,
    this.staff,
    this.role,
    this.branchId,
  });

  final bool ready;
  final bool hasWorkspace;
  final StaffMember? staff;
  final Role? role;

  /// Selected branch in the Company plan. Null means "all my branches".
  final String? branchId;

  bool get signedIn => staff != null;
  Set<String> get permissions =>
      (role?.permissions ?? '').split(',').where((e) => e.isNotEmpty).toSet();
  bool can(String perm) => permissions.contains(perm);
  bool get isOwner => role?.id == RoleIds.owner;

  /// Branch ids this person may work in (empty = unrestricted).
  Set<String> get branchLimit =>
      (staff?.branchIds ?? '').split(',').where((e) => e.isNotEmpty).toSet();

  /// Site ids a supervisor is limited to (empty = all sites).
  Set<String> get siteLimit =>
      (staff?.siteIds ?? '').split(',').where((e) => e.isNotEmpty).toSet();

  SessionState copyWith({
    bool? ready,
    bool? hasWorkspace,
    StaffMember? staff,
    Role? role,
    Object? branchId = _keep,
    bool clearUser = false,
  }) =>
      SessionState(
        ready: ready ?? this.ready,
        hasWorkspace: hasWorkspace ?? this.hasWorkspace,
        staff: clearUser ? null : (staff ?? this.staff),
        role: clearUser ? null : (role ?? this.role),
        branchId: identical(branchId, _keep) ? this.branchId : branchId as String?,
      );

  static const _keep = Object();
}

class SessionNotifier extends Notifier<SessionState> {
  static const _kStaff = 'session.staffId';
  static const _kBranch = 'session.branchId';

  @override
  SessionState build() => const SessionState();

  WorkspaceService get _ws => ref.read(workspaceServiceProvider);
  SharedPreferences get _prefs => ref.read(prefsProvider);

  Future<void> bootstrap() async {
    final has = await _ws.hasWorkspace();
    if (!has) {
      state = const SessionState(ready: true, hasWorkspace: false);
      return;
    }
    final id = _prefs.getString(_kStaff);
    final staff = id == null ? null : await _ws.staffById(id);
    if (staff != null && staff.isActive) {
      final role = await _ws.roleById(staff.roleId);
      state = SessionState(
        ready: true,
        hasWorkspace: true,
        staff: staff,
        role: role,
        branchId: _prefs.getString(_kBranch),
      );
    } else {
      state = const SessionState(ready: true, hasWorkspace: true);
    }
  }

  Future<void> signUp({
    required Plan plan,
    required String businessName,
    required String ownerName,
    required String email,
    required String mobile,
    required String password,
  }) async {
    final staff = await _ws.createWorkspace(
      plan: plan,
      businessName: businessName,
      ownerName: ownerName,
      email: email,
      mobile: mobile,
      password: password,
    );
    await _enter(staff);
  }

  /// Returns false when the credentials are wrong.
  Future<bool> login(String identifier, String secret) async {
    final staff = await _ws.login(identifier, secret);
    if (staff == null) return false;
    await _enter(staff);
    return true;
  }

  Future<void> _enter(StaffMember staff) async {
    final role = await _ws.roleById(staff.roleId);
    await _prefs.setString(_kStaff, staff.id);
    state = SessionState(
      ready: true,
      hasWorkspace: true,
      staff: staff,
      role: role,
      branchId: _prefs.getString(_kBranch),
    );
  }

  Future<void> logout() async {
    await _prefs.remove(_kStaff);
    state = const SessionState(ready: true, hasWorkspace: true);
  }

  /// Re-reads the signed-in person and their role (after role/team edits).
  Future<void> reload() async {
    final s = state.staff;
    if (s == null) return;
    final fresh = await _ws.staffById(s.id);
    if (fresh == null || !fresh.isActive) return logout();
    final role = await _ws.roleById(fresh.roleId);
    state = state.copyWith(staff: fresh, role: role);
  }

  Future<void> switchBranch(String? id) async {
    if (id == null) {
      await _prefs.remove(_kBranch);
    } else {
      await _prefs.setString(_kBranch, id);
    }
    state = state.copyWith(branchId: id);
  }
}

final sessionProvider = NotifierProvider<SessionNotifier, SessionState>(SessionNotifier.new);

/// The branch to filter queries by, or null for "everything I can see".
final scopeBranchProvider = Provider<String?>((ref) {
  final session = ref.watch(sessionProvider);
  final branches = ref.watch(branchesProvider).value ?? const <Branch>[];
  final limit = session.branchLimit;
  final allowed = limit.isEmpty ? branches : branches.where((b) => limit.contains(b.id)).toList();
  if (allowed.isEmpty) return null;
  final plan = ref.watch(planProvider);
  if (!plan.multiBranch || allowed.length == 1) return allowed.first.id;
  final chosen = session.branchId;
  if (chosen != null && allowed.any((b) => b.id == chosen)) return chosen;
  // Restricted staff cannot see "all".
  return limit.isEmpty ? null : allowed.first.id;
});

/// Branches the signed-in person may choose between.
final allowedBranchesProvider = Provider<List<Branch>>((ref) {
  final session = ref.watch(sessionProvider);
  final branches = ref.watch(branchesProvider).value ?? const <Branch>[];
  final limit = session.branchLimit;
  return limit.isEmpty ? branches : branches.where((b) => limit.contains(b.id)).toList();
});

/// Branch new records are created in. In "all branches" view it falls back to
/// the first allowed branch; forms let the owner pick another.
final writeBranchProvider = Provider<String?>((ref) {
  final scope = ref.watch(scopeBranchProvider);
  if (scope != null) return scope;
  final allowed = ref.watch(allowedBranchesProvider);
  return allowed.isEmpty ? null : allowed.first.id;
});

// ---- shared data ---------------------------------------------------------------

/// The day shown on Home / Attendance, shared so tabs stay in step.
class SelectedDate extends Notifier<DateTime> {
  @override
  DateTime build() => D.dateOnly(DateTime.now());
  void set(DateTime d) => state = D.dateOnly(d);
}

final selectedDateProvider = NotifierProvider<SelectedDate, DateTime>(SelectedDate.new);

final dashboardProvider = FutureProvider.autoDispose<DashboardData>((ref) async {
  ref.watch(dbTickProvider);
  final date = ref.watch(selectedDateProvider);
  final branch = ref.watch(scopeBranchProvider);
  return ref.watch(dashboardServiceProvider).load(date, branchId: branch);
});

/// Sites the signed-in person may take attendance for, with their sheets.
final siteSheetsProvider = FutureProvider.autoDispose<List<Sheet>>((ref) async {
  ref.watch(dbTickProvider);
  final date = ref.watch(selectedDateProvider);
  final branch = ref.watch(scopeBranchProvider);
  final limit = ref.watch(sessionProvider).siteLimit;
  var sites = await ref.watch(companyServiceProvider).sites(branchId: branch);
  if (limit.isNotEmpty) sites = sites.where((s) => limit.contains(s.id)).toList();
  final att = ref.watch(attendanceServiceProvider);
  final ymd = D.ymd(date);
  return [for (final s in sites) await att.loadSheet(s.id, ymd)];
});

/// Statuses are a comma separated string (not a Set) so the key has value
/// equality and the provider is not recreated on every rebuild.
typedef LabourQuery = ({String query, String statuses});

final labourListProvider = FutureProvider.autoDispose.family<List<Labour>, LabourQuery>((ref, q) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  final limit = ref.watch(sessionProvider).siteLimit;
  return ref.watch(labourServiceProvider).list(
        branchId: branch,
        statuses: q.statuses.split(',').where((e) => e.isNotEmpty).toSet(),
        query: q.query,
        siteIds: limit.isEmpty ? null : limit,
      );
});

final balancesProvider = FutureProvider.autoDispose<Map<String, int>>((ref) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  return ref.watch(paymentServiceProvider).balances(branchId: branch);
});

final companiesProvider = FutureProvider.autoDispose<List<Company>>((ref) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  return ref.watch(companyServiceProvider).companies(branchId: branch);
});

final invoicesProvider = FutureProvider.autoDispose<List<InvoiceRow>>((ref) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  return ref.watch(billingServiceProvider).list(branchId: branch);
});

String? lastStaffId(SharedPreferences p) => p.getString('session.staffId');

/// Past days (last 7) that still have a site with workers but no submitted
/// sheet, newest first. Lets the owner catch up on forgotten attendance.
final pendingDaysProvider = FutureProvider.autoDispose<List<DateTime>>((ref) async {
  ref.watch(dbTickProvider);
  final branch = ref.watch(scopeBranchProvider);
  final limit = ref.watch(sessionProvider).siteLimit;
  var sites = await ref.watch(companyServiceProvider).sites(branchId: branch);
  if (limit.isNotEmpty) sites = sites.where((s) => limit.contains(s.id)).toList();
  final att = ref.watch(attendanceServiceProvider);
  final out = <DateTime>[];
  for (var i = 1; i <= 7; i++) {
    final d = D.addDays(DateTime.now(), -i);
    final ymd = D.ymd(d);
    for (final s in sites) {
      final sheet = await att.loadSheet(s.id, ymd);
      if (sheet.total > 0 && !sheet.isSubmitted) {
        out.add(D.dateOnly(d));
        break;
      }
    }
  }
  return out;
});
