import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/theme.dart';
import 'core/widgets.dart';
import 'features/auth/login_screen.dart';
import 'features/auth/welcome_screen.dart';
import 'features/home/shell.dart';
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
    final Widget home;
    if (!session.ready) {
      home = const Scaffold(body: LoadingView());
    } else if (!session.hasWorkspace) {
      home = const WelcomeScreen();
    } else if (!session.signedIn) {
      home = const LoginScreen();
    } else {
      home = const Shell();
    }
    return MaterialApp(
      title: 'LabourBook',
      debugShowCheckedModeBanner: false,
      theme: buildTheme(),
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
