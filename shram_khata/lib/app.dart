import 'core/i18n.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/theme.dart';
import 'core/widgets.dart';
import 'features/auth/lock_screen.dart';
import 'features/auth/login_screen.dart';
import 'features/auth/welcome_screen.dart';
import 'core/money.dart';
import 'features/home/shell.dart';
import 'features/language/language_screen.dart';
import 'features/manager/manager_shell.dart';
import 'features/setup/setup_wizard_screen.dart';
import 'state/cloud.dart';
import 'state/providers.dart';

class ShramKhataApp extends ConsumerStatefulWidget {
  const ShramKhataApp({super.key});

  @override
  ConsumerState<ShramKhataApp> createState() => _ShramKhataAppState();
}

class _ShramKhataAppState extends ConsumerState<ShramKhataApp> with WidgetsBindingObserver {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    Future.microtask(() => ref.read(sessionProvider.notifier).bootstrap());
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  /// Catch up with the cloud whenever the app comes back to the screen, and
  /// lock again if the app was away for a while.
  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    final lock = ref.read(appLockProvider.notifier);
    if (state == AppLifecycleState.paused) lock.leftApp();
    if (state == AppLifecycleState.resumed) {
      lock.returned();
      ref.read(cloudProvider.notifier).syncNow();
    }
  }

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionProvider);
    final cloud = ref.watch(cloudProvider);
    final lock = ref.watch(appLockProvider);
    final lang = ref.watch(languageProvider);
    final langChosen = ref.watch(languageChosenProvider);
    final profile = ref.watch(profileProvider).value;
    final companies = ref.watch(companiesProvider).value;
    if (profile != null) {
      Palette.apply(Color(profile.themeColor));
      Money.configureCode(profile.currencyCode);
    }
    final needsSetup = session.isOwner &&
        profile != null &&
        companies != null &&
        !profile.setupDone &&
        companies.isEmpty;
    final Widget home;
    if (!session.ready) {
      home = const Scaffold(body: LoadingView());
    } else if (cloud.session?.isManager == true) {
      home = const ManagerShell();
    } else if (!session.hasWorkspace && !langChosen) {
      home = const LanguageScreen(first: true);
    } else if (!session.hasWorkspace) {
      home = const WelcomeScreen();
    } else if (!session.signedIn) {
      home = const LoginScreen();
    } else if (lock.locked) {
      home = const LockScreen();
    } else if (session.isOwner && (profile == null || companies == null)) {
      home = const Scaffold(body: LoadingView());
    } else if (needsSetup) {
      home = const SetupWizardScreen();
    } else {
      home = const Shell();
    }
    return MaterialApp(
      title: tr('HazriBook'),
      debugShowCheckedModeBanner: false,
      theme: buildTheme(),
      builder: (context, child) => KeyedSubtree(
        key: ValueKey('${profile?.themeColor}-${profile?.currencyCode}-$lang'),
        // Phones draw behind the system navigation bar; keep content above it.
        child: ColoredBox(
          color: Palette.surface,
          child: SafeArea(top: false, child: child ?? const SizedBox.shrink()),
        ),
      ),
      home: AnimatedSwitcher(
        duration: const Duration(milliseconds: 250),
        child: KeyedSubtree(
          key: ValueKey('${session.ready}-${session.hasWorkspace}-${session.signedIn}-$langChosen-${cloud.session?.isManager}-${lock.locked}'),
          child: home,
        ),
      ),
    );
  }
}
