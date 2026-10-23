import 'dart:io';

import 'package:drift/native.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/services/company_service.dart';
import 'package:shram_khata/data/sync/sync_store.dart';
import 'package:sqlite3/sqlite3.dart' as sql;

import 'support/seed.dart';

void main() {
  test('a database from app version 1 upgrades and keeps its data', () async {
    final dir = Directory.systemTemp.createTempSync('hb_mig');
    final file = File('${dir.path}/old.sqlite');
    addTearDown(() => dir.deleteSync(recursive: true));

    // Build a fresh database, then strip it back to what version 1 looked like.
    var db = AppDatabase(NativeDatabase(file));
    final s = await seed(db);
    final labours = (await db.select(db.labours).get()).length;
    await db.close();

    final raw = sql.sqlite3.open(file.path);
    for (final c in ['service_fee_type', 'service_fee_percent', 'service_fee_fixed']) {
      raw.execute('ALTER TABLE contracts DROP COLUMN $c');
    }
    for (final c in ['service_fee_type', 'service_fee_percent', 'service_fee_amount']) {
      raw.execute('ALTER TABLE invoices DROP COLUMN $c');
    }
    raw.execute('ALTER TABLE labour_rates DROP COLUMN duty_hours');
    final triggers = raw.select("SELECT name FROM sqlite_master WHERE type = 'trigger' AND name LIKE 'sync_%'");
    for (final r in triggers) {
      raw.execute('DROP TRIGGER ${r['name']}');
    }
    raw.execute('DROP TABLE sync_outbox');
    raw.execute('DROP TABLE sync_meta');
    raw.execute('PRAGMA user_version = 1');
    raw.close();

    db = AppDatabase(NativeDatabase(file));
    addTearDown(db.close);
    expect((await db.select(db.labours).get()).length, labours);
    final contracts = await CompanyService(db).contracts(s.companyA);
    expect(contracts.first.contract.serviceFeeType, 'none');
    final store = SyncStore(db);
    await store.setEnabled(true);
    await store.enqueueAll();
    expect(await store.pendingCount(), greaterThan(50));
  });
}
