// Runs the app's sync code against a real HazriBook server. Skipped unless
// HAZRIBOOK_TEST_URL is set, e.g. http://127.0.0.1:8099 (needs a clean database).
import 'dart:io';

import 'package:drift/drift.dart' hide isNotNull, isNull;
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/data/sync/api_client.dart';
import 'package:shram_khata/data/sync/sync_service.dart';

import 'support/seed.dart';

Future<int> count(AppDatabase db, String table) async =>
    (await db.customSelect('SELECT COUNT(*) AS n FROM $table').getSingle()).read<int>('n');

void main() {
  final url = Platform.environment['HAZRIBOOK_TEST_URL'];

  test('two phones, a supervisor and a company manager share one workspace', () async {
    SharedPreferences.setMockInitialValues({});
    final prefsA = await SharedPreferences.getInstance();
    final a = AppDatabase.inMemory();
    final s = await seed(a);
    final svcA = SyncService(a, prefsA, baseUrl: url!);

    // Phone A turns its workspace into a cloud account and uploads it.
    await svcA.register(
      businessName: 'Shree Ganesh Manpower',
      ownerName: 'Rajendra Vairagi',
      email: 'owner@example.com',
      mobile: '9876543210',
      password: 'cloudpass1',
      ownerId: s.ownerId,
      agencyId: '11111111-2222-4333-8444-555555555555',
    );
    final up = await svcA.sync();
    expect(up!.pushed, greaterThan(100));
    expect(await svcA.unsent(), 0);

    // Phone B (empty) signs in as the owner and downloads everything.
    SharedPreferences.setMockInitialValues({});
    final prefsB = await SharedPreferences.getInstance();
    final b = AppDatabase.inMemory();
    final svcB = SyncService(b, prefsB, baseUrl: url);
    final me = await svcB.login('owner@example.com', 'cloudpass1');
    expect(me.userId, s.ownerId);
    await svcB.sync();
    for (final t in ['labours', 'attendance', 'payments', 'invoices', 'companies', 'staff_members', 'roles']) {
      expect(await count(b, t), await count(a, t), reason: t);
    }

    // A change on B reaches A.
    await b.customStatement("UPDATE labours SET note = 'edited on B' WHERE id = ?", [s.labourIds.first]);
    await svcB.sync();
    await svcA.sync();
    final onA = await (a.select(a.labours)..where((t) => t.id.equals(s.labourIds.first))).getSingle();
    expect(onA.note, 'edited on B');

    // A supervisor login: sees data but no password hashes, cannot write roles.
    final staffId = '22222222-2222-4333-8444-555555555555';
    await svcA.createPerson(id: staffId, name: 'Sup', kind: 'staff', password: 'staffpass1', mobile: '9000000001');
    SharedPreferences.setMockInitialValues({});
    final c = AppDatabase.inMemory();
    final svcC = SyncService(c, await SharedPreferences.getInstance(), baseUrl: url);
    final sup = await svcC.login('9000000001', 'staffpass1');
    expect(sup.kind, 'staff');
    await svcC.sync();
    final hashes = await c.customSelect("SELECT COUNT(*) AS n FROM staff_members WHERE secret_hash <> ''").getSingle();
    expect(hashes.read<int>('n'), 0);
    expect(await count(c, 'attendance'), await count(a, 'attendance'));

    // A company manager sees only their company, without pay data or phone numbers.
    final mgrId = '33333333-2222-4333-8444-555555555555';
    await svcA.createPerson(
        id: mgrId, name: 'Mr. Verma', kind: 'manager', password: 'mgrpass123', email: 'verma@m.com', scopeCompanyId: s.companyA);
    SharedPreferences.setMockInitialValues({});
    final m = AppDatabase.inMemory();
    final svcM = SyncService(m, await SharedPreferences.getInstance(), baseUrl: url);
    final mgr = await svcM.login('verma@m.com', 'mgrpass123');
    expect(mgr.isManager, isTrue);
    await svcM.sync();
    expect(await count(m, 'payments'), 0);
    expect(await count(m, 'staff_members'), 0);
    expect(await count(m, 'companies'), 1);
    expect(await count(m, 'sites'), 2);
    expect(await count(m, 'labours'), greaterThan(0));
    final phones = await m.customSelect("SELECT COUNT(*) AS n FROM labours WHERE mobile <> ''").getSingle();
    expect(phones.read<int>('n'), 0);
    final otherCo = await m.customSelect("SELECT COUNT(*) AS n FROM attendance a JOIN sites s ON s.id = a.site_id WHERE s.company_id <> ?",
        variables: [Variable.withString(s.companyA)]).getSingle();
    expect(otherCo.read<int>('n'), 0);

    // Feedback: manager writes, owner is told.
    await svcM.sendFeedback('Raju came late twice this week.', labourId: s.labourIds.first);
    final inbox = await svcA.feedback();
    expect(inbox.unread, 1);
    expect(inbox.items.single.message, contains('Raju'));
    await svcA.markFeedbackRead([inbox.items.single.id]);
    expect((await svcA.feedback()).unread, 0);
    await expectLater(svcC.sendFeedback('not allowed'), throwsA(isA<ApiException>()));

    // Deleting the cloud account removes the server copy but keeps the phone's data.
    final before = await count(a, 'labours');
    await expectLater(svcA.deleteCloudAccount('wrong-password'), throwsA(isA<ApiException>()));
    await svcA.deleteCloudAccount('cloudpass1');
    expect(svcA.session, isNull);
    expect(await count(a, 'labours'), before);
    await expectLater(svcB.login('owner@example.com', 'cloudpass1'), throwsA(isA<ApiException>()));
    await expectLater(svcC.sync(), throwsA(isA<ApiException>()));
  }, skip: url == null ? 'set HAZRIBOOK_TEST_URL to run' : false, timeout: const Timeout(Duration(minutes: 3)));
}

