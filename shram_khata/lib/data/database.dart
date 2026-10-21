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
  int get schemaVersion => 2;

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
        },
        beforeOpen: (details) async {
          await customStatement('PRAGMA foreign_keys = ON');
        },
      );
}
