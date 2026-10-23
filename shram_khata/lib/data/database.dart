import 'dart:io';

import 'package:drift/drift.dart';
import 'package:drift/native.dart';
import 'package:path/path.dart' as p;
import 'package:path_provider/path_provider.dart';

import 'tables.dart';

part 'database.g.dart';

@DriftDatabase(tables: [
  Branches,
  Roles,
  StaffMembers,
  Companies,
  Sites,
  Contracts,
  ContractRates,
  Labours,
  LabourRates,
  LabourDocuments,
  Assignments,
  Attendance,
  AttendanceSheets,
  Payments,
  Invoices,
  InvoiceLines,
  InvoicePayments,
  AuditLogs,
  KeyValues,
])
class AppDatabase extends _$AppDatabase {
  AppDatabase(super.e);

  /// Opens (or creates) the on-device database file.
  static Future<AppDatabase> openOnDevice() async {
    final dir = await getApplicationDocumentsDirectory();
    final file = File(p.join(dir.path, 'shram_khata.sqlite'));
    return AppDatabase(NativeDatabase.createInBackground(file));
  }

  static AppDatabase inMemory() => AppDatabase(NativeDatabase.memory());

  @override
  int get schemaVersion => 4;

  @override
  MigrationStrategy get migration => MigrationStrategy(
        onCreate: (m) async {
          await m.createAll();
          await customStatement(
              'CREATE INDEX idx_att_date ON attendance (date)');
          await customStatement(
              'CREATE INDEX idx_att_labour ON attendance (labour_id, date)');
          await customStatement(
              'CREATE INDEX idx_pay_labour ON payments (labour_id, date)');
          await _createSyncObjects();
        },
        onUpgrade: (m, from, to) async {
          if (from < 2) {
            await m.addColumn(contracts, contracts.serviceFeeType);
            await m.addColumn(contracts, contracts.serviceFeePercent);
            await m.addColumn(contracts, contracts.serviceFeeFixed);
            await m.addColumn(invoices, invoices.serviceFeeType);
            await m.addColumn(invoices, invoices.serviceFeePercent);
            await m.addColumn(invoices, invoices.serviceFeeAmount);
          }
          if (from < 3) await _createSyncObjects();
          if (from < 4) await m.addColumn(labourRates, labourRates.dutyHours);
        },
        beforeOpen: (details) async {
          await customStatement('PRAGMA foreign_keys = ON');
        },
      );

  /// Cloud sync bookkeeping: a queue of changed rows and triggers that fill it.
  /// The queue only collects while sync is switched on, and never for rows
  /// that were just downloaded.
  Future<void> _createSyncObjects() async {
    await customStatement('CREATE TABLE IF NOT EXISTS sync_outbox ('
        'id INTEGER PRIMARY KEY AUTOINCREMENT, tbl TEXT NOT NULL, rid TEXT NOT NULL, op TEXT NOT NULL)');
    await customStatement('CREATE TABLE IF NOT EXISTS sync_meta (k TEXT PRIMARY KEY, v TEXT NOT NULL)');
    await customStatement("INSERT OR IGNORE INTO sync_meta (k, v) VALUES ('applying', '0')");
    await customStatement("INSERT OR IGNORE INTO sync_meta (k, v) VALUES ('enabled', '0')");
    const when = "WHEN (SELECT v FROM sync_meta WHERE k = 'enabled') = '1' "
        "AND (SELECT v FROM sync_meta WHERE k = 'applying') = '0'";
    for (final t in allTables) {
      final n = t.actualTableName;
      final pk = t.$primaryKey.length == 1 ? t.$primaryKey.first.name : 'id';
      for (final e in [('ins', 'INSERT', 'NEW', 'u'), ('upd', 'UPDATE', 'NEW', 'u'), ('del', 'DELETE', 'OLD', 'd')]) {
        await customStatement('CREATE TRIGGER IF NOT EXISTS sync_${n}_${e.$1} AFTER ${e.$2} ON $n $when BEGIN '
            "INSERT INTO sync_outbox (tbl, rid, op) VALUES ('$n', ${e.$3}.$pk, '${e.$4}'); END");
      }
    }
  }
}
