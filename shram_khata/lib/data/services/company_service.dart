import 'package:drift/drift.dart';

import '../../core/dates.dart';
import '../database.dart';
import 'base.dart';

class ContractWithRates {
  ContractWithRates(this.contract, this.rates);
  final Contract contract;
  final List<ContractRate> rates;
}

class CompanyService extends Service {
  CompanyService(super.db);

  Future<List<Company>> companies({String? branchId, bool activeOnly = true}) {
    final q = db.select(db.companies)..orderBy([(t) => OrderingTerm.asc(t.name)]);
    q.where((t) {
      Expression<bool> e = const Constant(true);
      if (branchId != null) e = e & t.branchId.equals(branchId);
      if (activeOnly) e = e & t.isActive.equals(true);
      return e;
    });
    return q.get();
  }

  Future<Company?> company(String id) =>
      (db.select(db.companies)..where((t) => t.id.equals(id))).getSingleOrNull();

  Future<String> addCompany({
    required String branchId,
    required String name,
    String contactPerson = '',
    String mobile = '',
    String email = '',
    String address = '',
    String gstin = '',
  }) async {
    if (name.trim().isEmpty) throw AppException('Company name is required.');
    final id = newId();
    await db.into(db.companies).insert(CompaniesCompanion.insert(
          id: id,
          branchId: branchId,
          name: name.trim(),
          contactPerson: Value(contactPerson.trim()),
          mobile: Value(mobile.trim()),
          email: Value(email.trim()),
          address: Value(address.trim()),
          gstin: Value(gstin.trim().toUpperCase()),
        ));
    return id;
  }

  Future<void> updateCompany(Company c) async {
    if (c.name.trim().isEmpty) throw AppException('Company name is required.');
    await db.update(db.companies).replace(c);
  }

  // ---- sites -----------------------------------------------------------------

  Future<List<Site>> sites({String? companyId, String? branchId, bool activeOnly = true}) async {
    final q = db.select(db.sites).join([
      innerJoin(db.companies, db.companies.id.equalsExp(db.sites.companyId)),
    ]);
    Expression<bool> e = const Constant(true);
    if (companyId != null) e = e & db.sites.companyId.equals(companyId);
    if (branchId != null) e = e & db.companies.branchId.equals(branchId);
    if (activeOnly) e = e & db.sites.isActive.equals(true) & db.companies.isActive.equals(true);
    q.where(e);
    q.orderBy([OrderingTerm.asc(db.companies.name), OrderingTerm.asc(db.sites.name)]);
    return (await q.get()).map((r) => r.readTable(db.sites)).toList();
  }

  Future<String> addSite({required String companyId, required String name, String address = ''}) async {
    if (name.trim().isEmpty) throw AppException('Site name is required.');
    final id = newId();
    await db.into(db.sites).insert(SitesCompanion.insert(
          id: id,
          companyId: companyId,
          name: name.trim(),
          address: Value(address.trim()),
        ));
    return id;
  }

  Future<void> updateSite(Site s) async {
    if (s.name.trim().isEmpty) throw AppException('Site name is required.');
    await db.update(db.sites).replace(s);
  }

  Future<Site?> site(String id) =>
      (db.select(db.sites)..where((t) => t.id.equals(id))).getSingleOrNull();

  // ---- contracts -------------------------------------------------------------

  Future<List<ContractWithRates>> contracts(String companyId) async {
    final cs = await (db.select(db.contracts)
          ..where((t) => t.companyId.equals(companyId))
          ..orderBy([(t) => OrderingTerm.desc(t.startDate)]))
        .get();
    final out = <ContractWithRates>[];
    for (final c in cs) {
      final rs = await (db.select(db.contractRates)
            ..where((t) => t.contractId.equals(c.id))
            ..orderBy([(t) => OrderingTerm.asc(t.skill)]))
          .get();
      out.add(ContractWithRates(c, rs));
    }
    return out;
  }

  /// Creates or replaces a contract together with its per-skill billing rates.
  Future<String> saveContract({
    String? id,
    required String companyId,
    required String title,
    required String startDate,
    String? endDate,
    int paymentTermsDays = 30,
    String notes = '',
    String? docPath,
    bool isActive = true,
    required List<({String skill, int perDay, int otPerHour})> rates,
  }) async {
    if (title.trim().isEmpty) throw AppException('Contract title is required.');
    if (endDate != null && endDate.compareTo(startDate) < 0) {
      throw AppException('End date is before the start date.');
    }
    final cleaned = rates.where((r) => r.skill.trim().isNotEmpty).toList();
    final seen = <String>{};
    for (final r in cleaned) {
      if (r.perDay <= 0) throw AppException('Enter a billing rate for ${r.skill}.');
      if (!seen.add(r.skill.trim().toLowerCase())) {
        throw AppException('${r.skill} is listed twice.');
      }
    }
    final cid = id ?? newId();
    await db.transaction(() async {
      await db.into(db.contracts).insertOnConflictUpdate(ContractsCompanion.insert(
            id: cid,
            companyId: companyId,
            title: title.trim(),
            startDate: startDate,
            endDate: Value(endDate),
            paymentTermsDays: Value(paymentTermsDays),
            notes: Value(notes.trim()),
            docPath: Value(docPath),
            isActive: Value(isActive),
          ));
      await (db.delete(db.contractRates)..where((t) => t.contractId.equals(cid))).go();
      for (final r in cleaned) {
        await db.into(db.contractRates).insert(ContractRatesCompanion.insert(
              id: newId(),
              contractId: cid,
              skill: r.skill.trim(),
              perDay: r.perDay,
              otPerHour: Value(r.otPerHour),
            ));
      }
    });
    return cid;
  }

  /// Contracts that end within [days] days (still active).
  Future<List<(Company, Contract)>> expiringContracts({int days = 30, String? branchId}) async {
    final t = D.today();
    final l = D.ymd(D.addDays(DateTime.now(), days));
    final q = db.select(db.contracts).join([
      innerJoin(db.companies, db.companies.id.equalsExp(db.contracts.companyId)),
    ])
      ..where(db.contracts.isActive.equals(true) &
          db.contracts.endDate.isNotNull() &
          db.contracts.endDate.isSmallerOrEqualValue(l) &
          db.contracts.endDate.isBiggerOrEqualValue(t) &
          db.companies.isActive.equals(true));
    if (branchId != null) q.where(db.companies.branchId.equals(branchId));
    final rows = await q.get();
    return rows.map((r) => (r.readTable(db.companies), r.readTable(db.contracts))).toList();
  }
}
