import 'package:flutter/material.dart';

import '../../core/theme.dart';
import 'login_screen.dart';
import 'plan_screen.dart';

class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key, this.canLogin = false});

  /// Show the "I already have an account" link (only if a workspace exists).
  final bool canLogin;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Palette.brandDark,
      body: Column(
        children: [
          Expanded(
            child: Container(
              width: double.infinity,
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Palette.brand, Palette.brandDark],
                ),
              ),
              child: SafeArea(
                bottom: false,
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(28, 40, 28, 12),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: 64,
                        height: 64,
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: const Icon(Icons.groups_rounded, size: 36, color: Palette.brand),
                      ),
                      const SizedBox(height: 24),
                      const Text(
                        'HazriBook',
                        style: TextStyle(
                          fontSize: 34,
                          fontWeight: FontWeight.w800,
                          color: Colors.white,
                          height: 1.1,
                        ),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        'Attendance, payments and billing for your labour agency. One simple app.',
                        style: TextStyle(
                          fontSize: 16,
                          color: Colors.white.withValues(alpha: 0.85),
                          height: 1.45,
                        ),
                      ),
                      const Spacer(),
                      const _Feature(Icons.fact_check_outlined, 'Mark P / A / H in one tap, even offline'),
                      const _Feature(Icons.account_balance_wallet_outlined, 'Daily payments, advances and full ledger'),
                      const _Feature(Icons.receipt_long_outlined, 'Company bills and labour statements as PDF'),
                      const _Feature(Icons.insights_outlined, 'Live dashboard: present, absent, earned, spent'),
                      const SizedBox(height: 16),
                    ],
                  ),
                ),
              ),
            ),
          ),
          Container(
            color: Palette.brandDark,
            padding: const EdgeInsets.fromLTRB(24, 8, 24, 16),
            child: SafeArea(
              top: false,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  FilledButton(
                    style: FilledButton.styleFrom(
                      backgroundColor: Palette.accent,
                      foregroundColor: Palette.ink,
                    ),
                    onPressed: () => Navigator.push(
                        context, MaterialPageRoute(builder: (_) => const PlanScreen())),
                    child: const Text('Get started'),
                  ),
                  if (canLogin)
                    TextButton(
                      style: TextButton.styleFrom(foregroundColor: Colors.white),
                      onPressed: () => Navigator.push(
                          context, MaterialPageRoute(builder: (_) => const LoginScreen())),
                      child: const Text('I already have an account'),
                    ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _Feature extends StatelessWidget {
  const _Feature(this.icon, this.text);
  final IconData icon;
  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 7),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.14),
              borderRadius: BorderRadius.circular(11),
            ),
            child: Icon(icon, size: 20, color: Colors.white),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Text(text, style: const TextStyle(color: Colors.white, fontSize: 14.5, height: 1.3)),
          ),
        ],
      ),
    );
  }
}
