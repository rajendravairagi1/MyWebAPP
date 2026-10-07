import 'package:drift/drift.dart' show Value;
import 'package:uuid/uuid.dart';

import '../../domain/business_profile.dart';
import '../../domain/calc.dart';
import '../database.dart';

const _uuid = Uuid();
String newId() => _uuid.v4();

/// Thrown for problems the user can fix (shown in a snackbar as-is).
class AppException implements Exception {
  AppException(this.message);
  final String message;
  @override
  String toString() => message;
}

/// Shared plumbing for the services below.
abstract class Service {
  Service(this.db);
  final AppDatabase db;

  Future<BusinessProfile> loadProfile() async {
    final row = await (db.select(db.keyValues)
          ..where((t) => t.key.equals('business')))
        .getSingleOrNull();
    return BusinessProfile.decode(row?.value);
  }

  /// Loads every rate that affects wages or billing. Cheap enough to do per
  /// screen for an agency-sized dataset.
  Future<RateBook> rateBook() async {
    final profile = await loadProfile();
    final results = await Future.wait([
      db.select(db.labourRates).get(),
      db.select(db.labours).get(),
      db.select(db.sites).get(),
      db.select(db.contracts).get(),
      db.select(db.contractRates).get(),
    ]);
    return RateBook(
      labourRates: results[0] as List<LabourRate>,
      labours: results[1] as List<Labour>,
      sites: results[2] as List<Site>,
      contracts: results[3] as List<Contract>,
      contractRates: results[4] as List<ContractRate>,
      monthDivisor: profile.monthDivisor,
    );
  }

  Future<void> audit(
    String? staffId,
    String action,
    String entity,
    String entityId, [
    String detail = '',
  ]) async {
    await db.into(db.auditLogs).insert(AuditLogsCompanion.insert(
          id: newId(),
          staffId: Value(staffId),
          action: action,
          entity: entity,
          entityId: Value(entityId),
          detail: Value(detail),
        ));
  }
}
