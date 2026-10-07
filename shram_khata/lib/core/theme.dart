import 'package:flutter/material.dart';

/// Colours with a fixed meaning across the app.
class Palette {
  Palette._();

  static const brand = Color(0xFF0F766E);
  static const brandDark = Color(0xFF0B4F4A);
  static const brandTint = Color(0xFFE7F3F1);
  static const accent = Color(0xFFF59E0B);

  static const bg = Color(0xFFF4F7F7);
  static const surface = Colors.white;
  static const ink = Color(0xFF111827);
  static const muted = Color(0xFF6B7280);
  static const line = Color(0xFFE5E9EC);

  static const present = Color(0xFF15803D);
  static const presentBg = Color(0xFFDCFCE7);
  static const half = Color(0xFFB45309);
  static const halfBg = Color(0xFFFEF3C7);
  static const absent = Color(0xFFB91C1C);
  static const absentBg = Color(0xFFFEE2E2);
  static const info = Color(0xFF1D4ED8);
  static const infoBg = Color(0xFFDBEAFE);

  static Color status(String s) => switch (s) {
        'P' => present,
        'H' => half,
        'A' => absent,
        _ => muted,
      };

  static Color statusBg(String s) => switch (s) {
        'P' => presentBg,
        'H' => halfBg,
        'A' => absentBg,
        _ => const Color(0xFFF1F3F4),
      };
}

ThemeData buildTheme() {
  final scheme = ColorScheme.fromSeed(
    seedColor: Palette.brand,
    brightness: Brightness.light,
  ).copyWith(
    primary: Palette.brand,
    onPrimary: Colors.white,
    primaryContainer: Palette.brandTint,
    onPrimaryContainer: Palette.brandDark,
    secondary: Palette.accent,
    surface: Palette.surface,
    onSurface: Palette.ink,
    outline: Palette.line,
    outlineVariant: Palette.line,
    error: Palette.absent,
  );

  const radius = 14.0;
  OutlineInputBorder border(Color c, [double w = 1]) => OutlineInputBorder(
        borderRadius: BorderRadius.circular(radius),
        borderSide: BorderSide(color: c, width: w),
      );

  final base = ThemeData(
    useMaterial3: true,
    colorScheme: scheme,
    fontFamily: 'NotoSans',
    fontFamilyFallback: const ['NotoSansDevanagari'],
    scaffoldBackgroundColor: Palette.bg,
    visualDensity: VisualDensity.standard,
  );

  return base.copyWith(
    textTheme: base.textTheme.apply(bodyColor: Palette.ink, displayColor: Palette.ink),
    appBarTheme: const AppBarTheme(
      backgroundColor: Palette.bg,
      foregroundColor: Palette.ink,
      elevation: 0,
      scrolledUnderElevation: 0,
      centerTitle: false,
      titleTextStyle: TextStyle(
        fontFamily: 'NotoSans',
        fontSize: 19,
        fontWeight: FontWeight.w700,
        color: Palette.ink,
      ),
    ),
    cardTheme: CardThemeData(
      color: Palette.surface,
      elevation: 0,
      margin: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: const BorderSide(color: Palette.line),
      ),
    ),
    dividerTheme: const DividerThemeData(color: Palette.line, thickness: 1, space: 1),
    inputDecorationTheme: InputDecorationTheme(
      filled: true,
      fillColor: Palette.surface,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
      border: border(Palette.line),
      enabledBorder: border(Palette.line),
      focusedBorder: border(Palette.brand, 1.6),
      errorBorder: border(Palette.absent),
      focusedErrorBorder: border(Palette.absent, 1.6),
      labelStyle: const TextStyle(color: Palette.muted),
    ),
    filledButtonTheme: FilledButtonThemeData(
      style: FilledButton.styleFrom(
        minimumSize: const Size.fromHeight(52),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(radius)),
        textStyle: const TextStyle(fontFamily: 'NotoSans', fontSize: 16, fontWeight: FontWeight.w700),
      ),
    ),
    outlinedButtonTheme: OutlinedButtonThemeData(
      style: OutlinedButton.styleFrom(
        minimumSize: const Size.fromHeight(52),
        foregroundColor: Palette.brand,
        side: const BorderSide(color: Palette.line),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(radius)),
        textStyle: const TextStyle(fontFamily: 'NotoSans', fontSize: 15, fontWeight: FontWeight.w600),
      ),
    ),
    textButtonTheme: TextButtonThemeData(
      style: TextButton.styleFrom(
        foregroundColor: Palette.brand,
        textStyle: const TextStyle(fontFamily: 'NotoSans', fontWeight: FontWeight.w600),
      ),
    ),
    chipTheme: ChipThemeData(
      backgroundColor: Palette.surface,
      selectedColor: Palette.brandTint,
      side: const BorderSide(color: Palette.line),
      labelStyle: const TextStyle(fontFamily: 'NotoSans', fontWeight: FontWeight.w600, fontSize: 13),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      showCheckmark: false,
    ),
    navigationBarTheme: NavigationBarThemeData(
      backgroundColor: Palette.surface,
      indicatorColor: Palette.brandTint,
      height: 68,
      labelTextStyle: WidgetStateProperty.resolveWith((s) => TextStyle(
            fontSize: 12,
            fontWeight: s.contains(WidgetState.selected) ? FontWeight.w700 : FontWeight.w500,
            color: s.contains(WidgetState.selected) ? Palette.brandDark : Palette.muted,
          )),
      iconTheme: WidgetStateProperty.resolveWith((s) => IconThemeData(
            color: s.contains(WidgetState.selected) ? Palette.brandDark : Palette.muted,
          )),
    ),
    floatingActionButtonTheme: FloatingActionButtonThemeData(
      backgroundColor: Palette.brand,
      foregroundColor: Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
    ),
    snackBarTheme: SnackBarThemeData(
      behavior: SnackBarBehavior.floating,
      backgroundColor: Palette.ink,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
    ),
    dialogTheme: DialogThemeData(
      backgroundColor: Palette.surface,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
    ),
    bottomSheetTheme: const BottomSheetThemeData(
      backgroundColor: Palette.surface,
      showDragHandle: true,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
    ),
    tabBarTheme: const TabBarThemeData(
      labelColor: Palette.brandDark,
      unselectedLabelColor: Palette.muted,
      indicatorColor: Palette.brand,
      labelStyle: TextStyle(fontWeight: FontWeight.w700, fontSize: 14),
      dividerColor: Palette.line,
    ),
    listTileTheme: const ListTileThemeData(
      contentPadding: EdgeInsets.symmetric(horizontal: 16),
    ),
  );
}
