import 'package:drift/drift.dart' hide isNotNull, isNull;
import 'package:flutter_test/flutter_test.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/services/attendance_service.dart';
import 'package:shram_khata/data/sync/sync_store.dart';

import 'support/seed.dart';

void main() {
  late AppDatabase db;
  late Seeded s;
  setUp(() async {
    db = AppDatabase.inMemory();
    s = await seed(db);
  });
  tearDown(() => db.close());

  test('nothing is queued until sync is switched on', () async {
    final store = SyncStore(db);
    expect(await store.pendingCount(), 0);
    await store.setEnabled(true);
    await AttendanceService(db).mark(siteId: s.siteA, date: '2026-10-05', labourId: s.labourIds.first, status: 'P');
    expect(await store.pendingCount(), 1);
  });

  test('enqueueAll uploads every row, acknowledge clears only what was sent', () async {
    final store = SyncStore(db);
    await store.setEnabled(true);
    await store.enqueueAll();
    final first = await store.pending(limit: 50);
    expect(first.changes.length, 50);
    expect(first.changes.first.data, isNotEmpty);
    await store.acknowledge(first);
    expect(await store.pendingCount(), greaterThan(0));
    // an edit while the batch was in flight stays queued
    final changed = first.changes.firstWhere((c) => c.table == 'labours');
    await db.customStatement('UPDATE labours SET note = ? WHERE id = ?', ['edited', changed.id]);
    final again = await store.pending(limit: 1000);
    expect(again.changes.any((c) => c.table == 'labours' && c.id == changed.id), isTrue);
  });

  test('deleted rows are sent as deletions', () async {
    final store = SyncStore(db);
    await store.setEnabled(true);
    await db.customStatement("DELETE FROM labour_documents");
    final id = (await db.select(db.attendance).get()).first.id;
    await store.acknowledge(await store.pending(limit: 5000));
    await db.customStatement('DELETE FROM attendance WHERE id = ?', [id]);
    final b = await store.pending();
    expect(b.changes.single.deleted, isTrue);
    expect(b.changes.single.id, id);
  });

  test('applying server rows does not queue them and upserts', () async {
    final store = SyncStore(db);
    await store.setEnabled(true);
    final before = await db.select(db.labours).get();
    final l = before.first;
    final full = (await db.customSelect('SELECT * FROM labours WHERE id = ?', variables: [Variable.withString(l.id)]).getSingle()).data;
    await store.apply([
      SyncChange(table: 'labours', id: l.id, data: {...full, 'name': 'From server'}),
      SyncChange(table: 'companies', id: 'new-co', data: {'branch_id': s.branchId, 'name': 'Server Co'}),
    ]);
    expect(await store.pendingCount(), 0);
    final row = await (db.select(db.labours)..where((t) => t.id.equals(l.id))).getSingle();
    expect(row.name, 'From server');
    expect(row.skill, l.skill);
    expect((await db.select(db.companies).get()).any((c) => c.id == 'new-co'), isTrue);
    await store.apply([SyncChange(table: 'companies', id: 'new-co', deleted: true)]);
    expect((await db.select(db.companies).get()).any((c) => c.id == 'new-co'), isFalse);
  });

  test('duplicate attendance from two phones converges to the smaller id', () async {
    final store = SyncStore(db);
    await store.setEnabled(true);
    final lab = s.labourIds.first;
    await AttendanceService(db).mark(siteId: s.siteA, date: '2026-09-01', labourId: lab, status: 'P');
    final mine = await (db.select(db.attendance)..where((t) => t.date.equals('2026-09-01') & t.labourId.equals(lab))).getSingle();
    await store.acknowledge(await store.pending(limit: 5000));

    // a row for the same person/site/day with a smaller id wins
    await store.apply([
      SyncChange(table: 'attendance', id: '0000-smaller', data: {
        'labour_id': lab, 'site_id': s.siteA, 'date': '2026-09-01', 'status': 'H', 'ot_hours': 0.0,
      }),
    ]);
    final rows = await (db.select(db.attendance)..where((t) => t.date.equals('2026-09-01') & t.labourId.equals(lab))).get();
    expect(rows.single.id, '0000-smaller');
    final b = await store.pending();
    expect(b.changes.any((c) => c.table == 'attendance' && c.id == mine.id && c.deleted), isTrue);

    // a row with a bigger id loses and is deleted on the server
    await store.acknowledge(b);
    await store.apply([
      SyncChange(table: 'attendance', id: 'zzzz-bigger', data: {
        'labour_id': lab, 'site_id': s.siteA, 'date': '2026-09-01', 'status': 'A', 'ot_hours': 0.0,
      }),
    ]);
    final after = await (db.select(db.attendance)..where((t) => t.date.equals('2026-09-01') & t.labourId.equals(lab))).get();
    expect(after.single.id, '0000-smaller');
    expect((await store.pending()).changes.single.id, 'zzzz-bigger');
  });

  test('staff rows without password hashes can be applied', () async {
    final store = SyncStore(db);
    final role = (await db.select(db.roles).get()).first;
    await store.apply([
      SyncChange(table: 'staff_members', id: 'st-1', data: {'name': 'Sup', 'role_id': role.id, 'mobile': '9'}),
    ]);
    final st = await (db.select(db.staffMembers)..where((t) => t.id.equals('st-1'))).getSingle();
    expect(st.secretHash, '');
  });

  test('an unsent local edit is not overwritten by a server row', () async {
    final store = SyncStore(db);
    await store.setEnabled(true);
    await store.acknowledge(await store.pending(limit: 5000));
    final l = (await db.select(db.labours).get()).first;
    await db.customStatement('UPDATE labours SET name = ? WHERE id = ?', ['Local edit', l.id]);
    final full = (await db.customSelect('SELECT * FROM labours WHERE id = ?', variables: [Variable.withString(l.id)]).getSingle()).data;
    await store.apply([SyncChange(table: 'labours', id: l.id, data: {...full, 'name': 'Old server value'})]);
    final row = await (db.select(db.labours)..where((t) => t.id.equals(l.id))).getSingle();
    expect(row.name, 'Local edit');
  });
}
