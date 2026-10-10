import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:shram_khata/app.dart';
import 'package:shram_khata/core/dates.dart';
import 'package:shram_khata/core/theme.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/features/attendance/attendance_sheet_screen.dart';
import 'package:shram_khata/features/billing/invoice_detail_screen.dart';
import 'package:shram_khata/features/companies/company_detail_screen.dart';
import 'package:shram_khata/features/labour/labour_detail_screen.dart';
import 'package:shram_khata/features/payments/payment_form_screen.dart';
import 'package:shram_khata/features/settings/business_profile_screen.dart';
import 'package:shram_khata/state/providers.dart';

import 'support/seed.dart';

// Screenshots land in $SHOTS_DIR (default: system temp). Run with --update-goldens.
final _shots = Platform.environment['SHOTS_DIR'] ?? Directory.systemTemp.path;

Future<void> loadFonts() async {
  Future<void> family(String name, List<String> files) async {
    final l = FontLoader(name);
    for (final f in files) {
      l.addFont(rootBundle.load(f));
    }
    await l.load();
  }

  const w = ['400Regular', '500Medium', '600SemiBold', '700Bold'];
  await family('NotoSans', [for (final x in w) 'assets/fonts/NotoSans_$x.ttf']);
  await family('NotoSansDevanagari', [for (final x in w) 'assets/fonts/NotoSansDevanagari_$x.ttf']);
  final root = Platform.environment['FLUTTER_ROOT'] ?? '/opt/sdk/flutter';
  final icons = File('$root/bin/cache/artifacts/material_fonts/MaterialIcons-Regular.otf');
  if (icons.existsSync()) {
    final l = FontLoader('MaterialIcons')..addFont(Future.value(ByteData.sublistView(icons.readAsBytesSync())));
    await l.load();
  }
}

Future<void> settle(WidgetTester t) async {
  for (var i = 0; i < 8; i++) {
    await t.runAsync(() => Future<void>.delayed(const Duration(milliseconds: 40)));
    await t.pump(const Duration(milliseconds: 120));
  }
}

Future<void> boot(WidgetTester t, AppDatabase db, {String? staffId}) async {
  SharedPreferences.setMockInitialValues({'session.staffId': ?staffId});
  final prefs = await t.runAsync(SharedPreferences.getInstance);
  t.view.physicalSize = const Size(1170, 2532);
  t.view.devicePixelRatio = 3;
  addTearDown(t.view.reset);
  await t.pumpWidget(ProviderScope(
    overrides: [
      databaseProvider.overrideWithValue(db),
      prefsProvider.overrideWithValue(prefs!),
    ],
    child: const ShramKhataApp(),
  ));
  await settle(t);
}

Future<void> shot(WidgetTester t, String name) async {
  await settle(t);
  final dir = Directory(_shots)..createSync(recursive: true);
  await expectLater(find.byType(MaterialApp), matchesGoldenFile('${dir.path}/$name.png'));
}

void main() {
  setUpAll(loadFonts);
  testWidgets('sign-up flow lands on the dashboard', (t) async {
    final db = AppDatabase.inMemory();
    addTearDown(() => t.runAsync(db.close));
    await boot(t, db);
    expect(find.text('HazriBook'), findsOneWidget);
    await shot(t, '01_welcome');

    await t.tap(find.text('Get started'));
    await settle(t);
    expect(find.text('Choose your plan'), findsOneWidget);
    await shot(t, '02_plans');

    await t.tap(find.textContaining('Continue with'));
    await settle(t);
    final fields = find.byType(TextFormField);
    await t.enterText(fields.at(0), 'Shree Ganesh Manpower');
    await t.enterText(fields.at(1), 'Rajendra');
    await t.enterText(fields.at(2), 'owner@example.com');
    await t.enterText(fields.at(3), '9876543210');
    await t.enterText(fields.at(4), 'secret1');
    await t.enterText(fields.at(5), 'secret1');
    await shot(t, '03_signup');
    await t.tap(find.text('Create account'));
    await t.runAsync(() => Future<void>.delayed(const Duration(milliseconds: 400)));
    await settle(t);
    expect(find.text('Your business'), findsOneWidget);
    await shot(t, '04_setup_country');
    await t.tap(find.text('Next'));
    await settle(t);
    expect(find.text('Your first company'), findsOneWidget);
    await t.enterText(find.byType(TextFormField).at(0), 'Tata Projects');
    await t.enterText(find.byType(TextFormField).at(2), 'Plant 1');
    await shot(t, '05_setup_company');
    await t.tap(find.text('Finish setup'));
    await t.runAsync(() => Future<void>.delayed(const Duration(milliseconds: 400)));
    await settle(t);
    expect(find.textContaining('Good'), findsWidgets);
    await shot(t, '06_dashboard_empty');
  });

  testWidgets('seeded app: main screens render', (t) async {
    final db = AppDatabase.inMemory();
    addTearDown(() => t.runAsync(db.close));
    late Seeded s;
    await t.runAsync(() async => s = await seed(db));
    await boot(t, db, staffId: s.ownerId);

    expect(find.textContaining('Good'), findsWidgets);
    await shot(t, '10_dashboard');

    await t.drag(find.byType(ListView).first, const Offset(0, -900));
    await shot(t, '11_dashboard_scrolled');

    await t.tap(find.text('Attendance').last);
    await settle(t);
    await shot(t, '12_attendance');

    await t.tap(find.text('Plant 3'));
    await settle(t);
    expect(find.byType(AttendanceSheetScreen), findsOneWidget);
    await shot(t, '13_sheet');
    // Mark someone present then back.
    await t.pageBack();
    await settle(t);

    await t.tap(find.text('Labour').last);
    await settle(t);
    await shot(t, '14_labour');

    await t.tap(find.text('Ramesh Kumar'));
    await settle(t);
    expect(find.byType(LabourDetailScreen), findsOneWidget);
    await shot(t, '15_labour_detail');
    await t.tap(find.text('Payments'));
    await settle(t);
    await shot(t, '16_labour_payments');
    await t.tap(find.text('Details'));
    await settle(t);
    await shot(t, '17_labour_details');
    await t.pageBack();
    await settle(t);

    await t.tap(find.text('Payments').last);
    await settle(t);
    await shot(t, '18_payments');
    await t.tap(find.text('Who to pay'));
    await settle(t);
    await shot(t, '19_who_to_pay');

    await t.tap(find.text('More').last);
    await settle(t);
    await shot(t, '20_more');
  });

  testWidgets('seeded app: detail screens render', (t) async {
    final db = AppDatabase.inMemory();
    addTearDown(() => t.runAsync(db.close));
    late Seeded s;
    await t.runAsync(() async => s = await seed(db));
    SharedPreferences.setMockInitialValues({'session.staffId': s.ownerId});
    final prefs = await t.runAsync(SharedPreferences.getInstance);
    t.view.physicalSize = const Size(1170, 2532);
    t.view.devicePixelRatio = 3;
    addTearDown(t.view.reset);

    Future<void> open(Widget w, String name) async {
      await t.pumpWidget(ProviderScope(
        key: UniqueKey(),
        overrides: [databaseProvider.overrideWithValue(db), prefsProvider.overrideWithValue(prefs!)],
        child: Consumer(builder: (context, ref, _) {
          // Sign in before showing the screen under test.
          ref.read(sessionProvider.notifier).bootstrap();
          final ready = ref.watch(sessionProvider).signedIn;
          return MaterialApp(theme: buildTheme(), home: ready ? w : const SizedBox());
        }),
      ));
      await settle(t);
      await shot(t, name);
    }

    await open(InvoiceDetailScreen(invoiceId: s.invoiceId), '30_invoice');
    await open(CompanyDetailScreen(companyId: s.companyA), '31_company');
    await open(const PaymentFormScreen(), '32_payment_form');
    await open(const BusinessProfileScreen(), '33_profile');
    expect(D.today(), isNotEmpty);
  });
}
