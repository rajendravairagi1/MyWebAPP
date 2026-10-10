import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/theme.dart';
import 'core/widgets.dart';
import 'features/auth/login_screen.dart';
import 'features/auth/welcome_screen.dart';
import 'core/money.dart';
import 'features/home/shell.dart';
import 'features/setup/setup_wizard_screen.dart';
import 'state/providers.dart';

class ShramKhataApp extends ConsumerStatefulWidget {
  const ShramKhataApp({super.key});

  @override
  ConsumerState<ShramKhataApp> createState() => _ShramKhataAppState();
}

class _ShramKhataAppState extends ConsumerState<ShramKhataApp> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => ref.read(sessionProvider.notifier).bootstrap());
  }

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionProvider);
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
    } else if (!session.hasWorkspace) {
      home = const WelcomeScreen();
    } else if (!session.signedIn) {
      home = const LoginScreen();
    } else if (session.isOwner && (profile == null || companies == null)) {
      home = const Scaffold(body: LoadingView());
    } else if (needsSetup) {
      home = const SetupWizardScreen();
    } else {
      home = const Shell();
    }
    return MaterialApp(
      title: 'HazriBook',
      debugShowCheckedModeBanner: false,
      theme: buildTheme(),
      builder: (context, child) => KeyedSubtree(
        key: ValueKey('${profile?.themeColor}-${profile?.currencyCode}'),
        child: child ?? const SizedBox.shrink(),
      ),
      home: AnimatedSwitcher(
        duration: const Duration(milliseconds: 250),
        child: KeyedSubtree(
          key: ValueKey('${session.ready}-${session.hasWorkspace}-${session.signedIn}'),
          child: home,
        ),
      ),
    );
  }
}
