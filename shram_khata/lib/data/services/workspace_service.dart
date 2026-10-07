import 'package:drift/drift.dart';

import '../../core/permissions.dart';
import '../../core/plans.dart';
import '../../core/security.dart';
import '../../domain/business_profile.dart';
import '../database.dart';
import 'base.dart';

/// Workspace = the agency. Handles sign-up, login, branches, team and roles.
class WorkspaceService extends Service {
  WorkspaceService(super.db);

  Future<bool> hasWorkspace() async {
    final c = await db.select(db.staffMembers).get();
    return c.isNotEmpty;
  }

  /// Creates the agency with its first branch, the built-in roles and the
  /// owner account.
  Future<StaffMember> createWorkspace({
    required Plan plan,
    required String businessName,
    required String ownerName,
    required String email,
    required String mobile,
    required String password,
  }) async {
    if (await hasWorkspace()) {
      throw AppException('A workspace already exists on this device.');
    }
    if (!Security.isEmail(email)) throw AppException('Enter a valid email.');
    if (password.length < 6) {
      throw AppException('Password must be at least 6 characters.');
    }
    final branchId = newId();
    final ownerId = newId();
    final salt = Security.newSalt();
    await db.transaction(() async {
      await db.into(db.branches).insert(BranchesCompanion.insert(
            id: branchId,
            name: plan.multiBranch ? 'Head Office' : 'Main',
          ));
      await _seedRoles();
      await db.into(db.staffMembers).insert(StaffMembersCompanion.insert(
            id: ownerId,
            name: ownerName.trim(),
            mobile: Value(mobile.trim()),
            email: Value(email.trim().toLowerCase()),
            roleId: RoleIds.owner,
            secretHash: Security.hash(password, salt),
            salt: salt,
          ));
      final profile = BusinessProfile(
        planId: plan.id,
        name: businessName.trim(),
        ownerName: ownerName.trim(),
        mobile: mobile.trim(),
        email: email.trim().toLowerCase(),
      );
      await saveProfile(profile);
      await audit(ownerId, 'create', 'workspace', branchId, plan.id);
    });
    return (await staffById(ownerId))!;
  }

  Future<void> _seedRoles() async {
    Future<void> put(String id, String name, List<String> perms) =>
        db.into(db.roles).insertOnConflictUpdate(RolesCompanion.insert(
              id: id,
              name: name,
              permissions: Value(perms.join(',')),
              isSystem: const Value(true),
            ));
    await put(RoleIds.owner, 'Owner', Perm.all);
    await put(RoleIds.supervisor, 'Supervisor', Perm.supervisor);
    await put(RoleIds.accountant, 'Accountant', Perm.accountant);
  }

  /// Matches by email or mobile number. Returns null on a wrong secret.
  Future<StaffMember?> login(String identifier, String secret) async {
    final id = identifier.trim().toLowerCase();
    if (id.isEmpty) return null;
    final all = await (db.select(db.staffMembers)
          ..where((t) => t.isActive.equals(true)))
        .get();
    for (final s in all) {
      if (s.email.toLowerCase() == id || (s.mobile.isNotEmpty && s.mobile == id)) {
        if (Security.verify(secret, s.salt, s.secretHash)) return s;
      }
    }
    return null;
  }

  Future<StaffMember?> staffById(String id) => (db.select(db.staffMembers)
        ..where((t) => t.id.equals(id)))
      .getSingleOrNull();

  Future<Role?> roleById(String id) =>
      (db.select(db.roles)..where((t) => t.id.equals(id))).getSingleOrNull();

  // ---- business profile -------------------------------------------------

  Future<BusinessProfile> profile() => loadProfile();

  Future<void> saveProfile(BusinessProfile p) =>
      db.into(db.keyValues).insertOnConflictUpdate(
          KeyValuesCompanion.insert(key: 'business', value: p.encode()));

  Future<void> changePlan(Plan plan, {String? staffId}) async {
    final staffCount = (await (db.select(db.staffMembers)
              ..where((t) => t.isActive.equals(true)))
            .get())
        .length;
    final branchCount = (await (db.select(db.branches)
              ..where((t) => t.isActive.equals(true)))
            .get())
        .length;
    if (staffCount > plan.maxStaff) {
      throw AppException(
          'Remove extra team members first. ${plan.title} allows ${plan.maxStaff} user(s).');
    }
    if (branchCount > 1 && !plan.multiBranch) {
      throw AppException('Deactivate extra branches before switching to ${plan.title}.');
    }
    final p = await profile();
    await saveProfile(p.copyWith(planId: plan.id));
    await audit(staffId, 'change_plan', 'workspace', '', plan.id);
  }

  // ---- branches -----------------------------------------------------------

  Future<List<Branch>> branches({bool activeOnly = true}) {
    final q = db.select(db.branches)..orderBy([(t) => OrderingTerm.asc(t.createdAt)]);
    if (activeOnly) q.where((t) => t.isActive.equals(true));
    return q.get();
  }

  Future<String> addBranch({
    required String name,
    String address = '',
    String city = '',
    String mobile = '',
  }) async {
    final plan = Plan.fromId((await profile()).planId);
    final existing = await branches();
    if (!plan.multiBranch && existing.isNotEmpty) {
      throw AppException('Your plan has a single branch. Upgrade to Company for more.');
    }
    if (name.trim().isEmpty) throw AppException('Branch name is required.');
    final id = newId();
    await db.into(db.branches).insert(BranchesCompanion.insert(
          id: id,
          name: name.trim(),
          address: Value(address.trim()),
          city: Value(city.trim()),
          mobile: Value(mobile.trim()),
        ));
    return id;
  }

  Future<void> updateBranch(Branch b) => db.update(db.branches).replace(b);

  // ---- team ------------------------------------------------------------------

  Future<List<StaffMember>> staff() => (db.select(db.staffMembers)
        ..orderBy([(t) => OrderingTerm.asc(t.createdAt)]))
      .get();

  Future<List<Role>> roles() => (db.select(db.roles)
        ..orderBy([(t) => OrderingTerm.desc(t.isSystem), (t) => OrderingTerm.asc(t.name)]))
      .get();

  Future<String> addStaff({
    required String name,
    required String mobile,
    String email = '',
    required String roleId,
    required String pin,
    List<String> branchIds = const [],
    List<String> siteIds = const [],
  }) async {
    final plan = Plan.fromId((await profile()).planId);
    final active = (await staff()).where((s) => s.isActive).length;
    if (active >= plan.maxStaff) {
      throw AppException('Your ${plan.title} plan does not allow more team members.');
    }
    if (name.trim().isEmpty) throw AppException('Name is required.');
    if (mobile.trim().length < 10 && !Security.isEmail(email)) {
      throw AppException('Enter a 10-digit mobile number (used to log in).');
    }
    if (pin.length < 4) throw AppException('PIN must be at least 4 digits.');
    final dup = (await staff()).any((s) =>
        (mobile.isNotEmpty && s.mobile == mobile.trim()) ||
        (email.isNotEmpty && s.email.toLowerCase() == email.trim().toLowerCase()));
    if (dup) throw AppException('Someone with this mobile/email already exists.');
    final id = newId();
    final salt = Security.newSalt();
    await db.into(db.staffMembers).insert(StaffMembersCompanion.insert(
          id: id,
          name: name.trim(),
          mobile: Value(mobile.trim()),
          email: Value(email.trim().toLowerCase()),
          roleId: roleId,
          secretHash: Security.hash(pin, salt),
          salt: salt,
          branchIds: Value(branchIds.join(',')),
          siteIds: Value(siteIds.join(',')),
        ));
    return id;
  }

  Future<void> updateStaff(
    String id, {
    String? name,
    String? mobile,
    String? roleId,
    String? pin,
    bool? isActive,
    List<String>? branchIds,
    List<String>? siteIds,
  }) async {
    final cur = await staffById(id);
    if (cur == null) return;
    if (cur.roleId == RoleIds.owner && (isActive == false || roleId != null && roleId != RoleIds.owner)) {
      throw AppException('The owner account cannot be disabled or changed.');
    }
    String? salt, hash;
    if (pin != null && pin.isNotEmpty) {
      if (pin.length < 4) throw AppException('PIN must be at least 4 digits.');
      salt = Security.newSalt();
      hash = Security.hash(pin, salt);
    }
    await (db.update(db.staffMembers)..where((t) => t.id.equals(id))).write(
      StaffMembersCompanion(
        name: name == null ? const Value.absent() : Value(name.trim()),
        mobile: mobile == null ? const Value.absent() : Value(mobile.trim()),
        roleId: roleId == null ? const Value.absent() : Value(roleId),
        isActive: isActive == null ? const Value.absent() : Value(isActive),
        branchIds: branchIds == null ? const Value.absent() : Value(branchIds.join(',')),
        siteIds: siteIds == null ? const Value.absent() : Value(siteIds.join(',')),
        secretHash: hash == null ? const Value.absent() : Value(hash),
        salt: salt == null ? const Value.absent() : Value(salt),
      ),
    );
  }

  Future<bool> changeOwnSecret(String staffId, String oldSecret, String newSecret) async {
    final s = await staffById(staffId);
    if (s == null || !Security.verify(oldSecret, s.salt, s.secretHash)) return false;
    if (newSecret.length < 4) throw AppException('Use at least 4 characters.');
    final salt = Security.newSalt();
    await (db.update(db.staffMembers)..where((t) => t.id.equals(staffId))).write(
      StaffMembersCompanion(
        salt: Value(salt),
        secretHash: Value(Security.hash(newSecret, salt)),
      ),
    );
    return true;
  }

  Future<String> saveRole({String? id, required String name, required List<String> permissions}) async {
    final plan = Plan.fromId((await profile()).planId);
    if (!plan.customRoles) {
      throw AppException('Custom roles are available on Owner + Team and Company plans.');
    }
    if (name.trim().isEmpty) throw AppException('Role name is required.');
    final rid = id ?? newId();
    if (id != null) {
      final r = await roleById(id);
      if (r?.isSystem ?? false) throw AppException('Built-in roles cannot be edited.');
    }
    await db.into(db.roles).insertOnConflictUpdate(RolesCompanion.insert(
          id: rid,
          name: name.trim(),
          permissions: Value(permissions.join(',')),
        ));
    return rid;
  }

  Future<void> deleteRole(String id) async {
    final r = await roleById(id);
    if (r == null) return;
    if (r.isSystem) throw AppException('Built-in roles cannot be deleted.');
    final used = (await staff()).any((s) => s.roleId == id);
    if (used) throw AppException('Move people off this role before deleting it.');
    await (db.delete(db.roles)..where((t) => t.id.equals(id))).go();
  }
}
