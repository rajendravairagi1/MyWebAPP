import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/state/providers.dart';

import 'support/seed.dart';

void main() {
  Future<(ProviderContainer, AppDatabase, Seeded, SharedPreferences)> setup(Map<String, Object> prefsInit) async {
    SharedPreferences.setMockInitialValues(prefsInit);
    final prefs = await SharedPreferences.getInstance();
    final db = AppDatabase.inMemory();
    final s = await seed(db);
    final c = ProviderContainer(overrides: [databaseProvider.overrideWithValue(db), prefsProvider.overrideWithValue(prefs)]);
    addTearDown(() async {
      c.dispose();
      await db.close();
    });
    return (c, db, s, prefs);
  }

  test('password is checked for the app lock', () async {
    final (c, _, s, _) = await setup({});
    final ws = c.read(workspaceServiceProvider);
    expect(await ws.verifySecret(s.ownerId, 'secret1'), isTrue);
    expect(await ws.verifySecret(s.ownerId, 'wrong'), isFalse);
    await ws.setSecret(s.ownerId, 'brandnew9');
    expect(await ws.verifySecret(s.ownerId, 'brandnew9'), isTrue);
    expect(await ws.verifySecret(s.ownerId, 'secret1'), isFalse);
  });

  test('lock starts locked only when switched on, and unlocks', () async {
    final (c, _, _, prefs) = await setup({'lock.enabled': true});
    expect(c.read(appLockProvider).locked, isTrue);
    c.read(appLockProvider.notifier).unlock();
    expect(c.read(appLockProvider).locked, isFalse);
    expect(c.read(appLockProvider).enabled, isTrue);
    await c.read(appLockProvider.notifier).setEnabled(false);
    expect(prefs.getBool('lock.enabled'), isFalse);
    // a quick trip out of the app does not lock again
    c.read(appLockProvider.notifier)
      ..leftApp()
      ..returned();
    expect(c.read(appLockProvider).locked, isFalse);
  });

  test('erase everything wipes data and sign-in but keeps the language', () async {
    final (c, db, s, prefs) = await setup({'session.staffId': 'x', 'app.lang': 'hi', 'lock.enabled': true});
    await c.read(sessionProvider.notifier).bootstrap();
    await c.read(sessionProvider.notifier).enterCloud(s.ownerId);
    expect(c.read(sessionProvider).signedIn, isTrue);
    await c.read(sessionProvider.notifier).eraseEverything();
    expect(c.read(sessionProvider).hasWorkspace, isFalse);
    expect(c.read(sessionProvider).signedIn, isFalse);
    expect((await db.select(db.labours).get()), isEmpty);
    expect((await db.select(db.staffMembers).get()), isEmpty);
    expect(prefs.getString('app.lang'), 'hi');
    expect(prefs.getString('session.staffId'), isNull);
    expect(prefs.getBool('lock.enabled'), isNull);
    expect(c.read(appLockProvider).enabled, isFalse);
  });
}
