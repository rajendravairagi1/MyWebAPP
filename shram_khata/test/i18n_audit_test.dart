// Opens most screens in Hindi and writes every text that has no translation to
// $SHOTS_DIR/i18n_misses.txt. Fails when something shown is still English.
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:shram_khata/core/i18n.dart';
import 'package:shram_khata/core/plans.dart';
import 'package:shram_khata/core/theme.dart';
import 'package:shram_khata/data/database.dart';
import 'package:shram_khata/features/attendance/attendance_screen.dart';
import 'package:shram_khata/features/attendance/attendance_sheet_screen.dart';
import 'package:shram_khata/features/auth/signup_screen.dart';
import 'package:shram_khata/features/auth/welcome_screen.dart';
import 'package:shram_khata/features/billing/invoice_create_screen.dart';
import 'package:shram_khata/features/billing/invoice_detail_screen.dart';
import 'package:shram_khata/features/billing/invoices_screen.dart';
import 'package:shram_khata/features/cloud/cloud_login_screen.dart';
import 'package:shram_khata/features/cloud/cloud_screen.dart';
import 'package:shram_khata/features/cloud/feedback_screen.dart';
import 'package:shram_khata/features/companies/companies_screen.dart';
import 'package:shram_khata/features/companies/company_detail_screen.dart';
import 'package:shram_khata/features/companies/contract_form_screen.dart';
import 'package:shram_khata/features/home/dashboard_screen.dart';
import 'package:shram_khata/features/home/shell.dart';
import 'package:shram_khata/features/labour/labour_detail_screen.dart';
import 'package:shram_khata/features/labour/labour_form_screen.dart';
import 'package:shram_khata/features/labour/labour_list_screen.dart';
import 'package:shram_khata/features/language/language_screen.dart';
import 'package:shram_khata/features/payments/payment_form_screen.dart';
import 'package:shram_khata/features/payments/payments_screen.dart';
import 'package:shram_khata/features/reports/reports_screen.dart';
import 'package:shram_khata/features/settings/account_screen.dart';
import 'package:shram_khata/features/settings/backup_screen.dart';
import 'package:shram_khata/features/settings/business_profile_screen.dart';
import 'package:shram_khata/features/settings/more_screen.dart';
import 'package:shram_khata/features/settings/plan_screen.dart';
import 'package:shram_khata/features/setup/setup_wizard_screen.dart';
import 'package:shram_khata/features/team/staff_form_screen.dart';
import 'package:shram_khata/features/team/team_screen.dart';
import 'package:shram_khata/state/providers.dart';

import 'support/seed.dart';

Future<void> _fonts() async {
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
}

void main() {
  setUpAll(_fonts);

  testWidgets('screens in Hindi leave nothing in English', (t) async {
    await t.runAsync(() => I18n.load('hi'));
    I18n.misses.clear();
    addTearDown(() => t.runAsync(() => I18n.load('en')));
    final db = AppDatabase.inMemory();
    addTearDown(() => t.runAsync(db.close));
    late Seeded s;
    await t.runAsync(() async => s = await seed(db));
    SharedPreferences.setMockInitialValues({'session.staffId': s.ownerId, 'app.lang': 'hi'});
    final prefs = await t.runAsync(SharedPreferences.getInstance);
    t.view.physicalSize = const Size(1170, 2532);
    t.view.devicePixelRatio = 3;
    addTearDown(t.view.reset);

    Future<void> open(Widget w) async {
      await t.pumpWidget(ProviderScope(
        key: UniqueKey(),
        overrides: [databaseProvider.overrideWithValue(db), prefsProvider.overrideWithValue(prefs!)],
        child: Consumer(builder: (context, ref, _) {
          ref.read(sessionProvider.notifier).bootstrap();
          final ready = ref.watch(sessionProvider).signedIn;
          return MaterialApp(theme: buildTheme(), home: ready ? w : const SizedBox());
        }),
      ));
      for (var i = 0; i < 8; i++) {
        await t.runAsync(() => Future<void>.delayed(const Duration(milliseconds: 40)));
        await t.pump(const Duration(milliseconds: 120));
      }
    }

    final screens = <Widget>[
      const Shell(),
      const DashboardScreen(),
      const AttendanceScreen(),
      AttendanceSheetScreen(siteId: s.siteA, date: DateTime.now().toIso8601String().substring(0, 10)),
      const LabourListScreen(),
      LabourDetailScreen(labourId: s.labourIds.first),
      const LabourFormScreen(),
      const PaymentsScreen(),
      const PaymentFormScreen(),
      const CompaniesScreen(),
      CompanyDetailScreen(companyId: s.companyA),
      ContractFormScreen(companyId: s.companyA),
      const InvoicesScreen(),
      const InvoiceCreateScreen(),
      InvoiceDetailScreen(invoiceId: s.invoiceId),
      const ReportsScreen(),
      const MoreScreen(),
      const BusinessProfileScreen(),
      const AccountScreen(),
      const BackupScreen(),
      const PlanSettingsScreen(),
      const TeamScreen(),
      const StaffFormScreen(),
      const CloudScreen(),
      const CloudLoginScreen(),
      const FeedbackScreen(),
      const LanguageScreen(),
      const SetupWizardScreen(),
      const WelcomeScreen(),
      const SignUpScreen(plan: Plan.solo),
    ];
    for (final w in screens) {
      await open(w);
      // also visit tabs of the worker screen
      for (final tab in find.byType(Tab).evaluate().toList()) {
        await t.tap(find.byWidget(tab.widget).first, warnIfMissed: false);
        await t.pump(const Duration(milliseconds: 300));
      }
    }

    final out = Platform.environment['SHOTS_DIR'] ?? Directory.systemTemp.path;
    // Already-translated text passing through tr() again, data in capitals and
    // names that stay the same in every language are not gaps.
    const sameEverywhere = {'IFSC', 'UPI ID', 'GST', 'PIN'};
    final list = I18n.misses
        .where((m) =>
            !RegExp(r'[^\x00-\x7F]').hasMatch(m) && m != m.toUpperCase() && !sameEverywhere.contains(m))
        .toList()
      ..sort();
    File('$out/i18n_misses.txt').writeAsStringSync(list.join('\n'));
    expect(list, isEmpty, reason: list.join(' | '));
  });
}
